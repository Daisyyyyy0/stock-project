import datetime
import numpy as np
import pandas as pd
import matplotlib.pyplot as plt
import warnings
import math
import mpld3
import base64
from io import BytesIO


warnings.simplefilter(action='ignore', category=FutureWarning)


def backtest(start_date, end_date, hold_days, strategy, data, weight='average', benchmark=None, stop_loss=None, stop_profit=None):

    # portfolio check
    if weight != 'average' and weight != 'price':
        print('Backtest stop, weight should be "average" or "price", find',
              weight, 'instead')

    # get price data in order backtest
    data.date = end_date
    price = data.get('收盤價', (end_date - start_date).days)
    # start from 1 TWD at start_date,
    end = 1
    date = start_date

    # record some history
    equality = pd.Series()
    nstock = {}
    transections = pd.DataFrame()
    maxreturn = -10000
    minreturn = 10000

    def trading_day(date):
        if date not in price.index:
            temp = price.loc[date:]
            if temp.empty:
                return price.index[-1]
            else:
                return temp.index[0]
        else:
            return date

    def date_iter_periodicity(start_date, end_date, hold_days):
        date = start_date
        while date < end_date:
            yield (date), (date + datetime.timedelta(hold_days))
            date += datetime.timedelta(hold_days)

    def date_iter_specify_dates(start_date, end_date, hold_days):
        dlist = [start_date] + hold_days + [end_date]
        if dlist[0] == dlist[1]:
            dlist = dlist[1:]
        if dlist[-1] == dlist[-2]:
            dlist = dlist[:-1]
        for sdate, edate in zip(dlist, dlist[1:]):
            yield (sdate), (edate)

    if isinstance(hold_days, int):
        dates = date_iter_periodicity(start_date, end_date, hold_days)
    elif isinstance(hold_days, list):
        dates = date_iter_specify_dates(start_date, end_date, hold_days)
    else:
        print('the type of hold_dates should be list or int.')
        return None

    sdate_list = []
    edate_list = []
    returns_word = []
    return_rates = []
    rows = []
    col = {}
    sumup = {  # sumup:上述所有list打包成一個字典
        "sdate": None,
        "edate": None,
        "returns_word": None,
        "return_rates": None
    }
    for sdate, edate in dates:

        # select stocks at date
        data.date = sdate
        stocks = strategy(data)

        # hold the stocks for hold_days day
        s = price[stocks.index & price.columns][sdate:edate].iloc[1:]

        if s.empty:
            s = pd.Series(1, index=pd.date_range(
                sdate + datetime.timedelta(days=1), edate))
        else:

            if stop_loss != None:
                below_stop = (
                    (s / s.bfill().iloc[0]) - 1)*100 < -np.abs(stop_loss)
                below_stop = (below_stop.cumsum() > 0).shift(2).fillna(False)
                s[below_stop] = np.nan

            if stop_profit != None:
                above_stop = (
                    (s / s.bfill().iloc[0]) - 1)*100 > np.abs(stop_profit)
                above_stop = (above_stop.cumsum() > 0).shift(2).fillna(False)
                s[above_stop] = np.nan

            s.dropna(axis=1, how='all', inplace=True)

            # record transections
            bprice = s.bfill().iloc[0]
            sprice = s.apply(lambda s: s.dropna().iloc[-1])
            transections = transections.append(pd.DataFrame({
                'buy_price': bprice,
                'sell_price': sprice,
                'lowest_price': s.min(),
                'highest_price': s.max(),
                'buy_date': pd.Series(s.index[0], index=s.columns),
                'sell_date': s.apply(lambda s: s.dropna().index[-1]),
                'profit(%)': (sprice/bprice - 1) * 100
            }))

            s.ffill(inplace=True)

            # calculate equality
            # normalize and average the price of each stocks
            if weight == 'average':
                s = s/s.bfill().iloc[0]
            s = s.mean(axis=1)
            s = s / s.bfill()[0]

        col['sdate'] = sdate
        col['edate'] = edate
        col['return_word'] = returns_word
        col['return_rates'] = return_rates
        # print(sdate)
        sdate_list.append(sdate)
        edate_list.append(edate)
        # print(sdate_list)
        returns_word.append('報酬率:')
        return_rates.append(s.iloc[-1]/s.iloc[0] * 100 - 100)
        sumup = {                                       # sumup:上述所有list打包成一個字典
            # "sdate": pd.to_datetime(sdate_list),
            # "edate": pd.to_datetime(edate_list),
            # "returns_word": returns_word,
            # "return_rates": return_rates
        }

        # print('----------row新增col---------')
        # print(rows)

        df_sumup = pd.DataFrame(
            rows, columns=["start_date", "end_date", "報酬率:", "return_rates"])
        # print('----------df_sumup---------')
        # print(df_sumup)

        # print some log

        print(sdate, '-', edate,
              '報酬率: %.2f' % (s.iloc[-1]/s.iloc[0] * 100 - 100),
              '%', 'nstock', len(stocks))

        maxreturn = max(maxreturn, s.iloc[-1]/s.iloc[0] * 100 - 100)
        minreturn = min(minreturn, s.iloc[-1]/s.iloc[0] * 100 - 100)

        # plot backtest result
        ((s*end-1)*100).plot()
        equality = equality.append(s*end)
        end = (s/s[0]*end).iloc[-1]

        if math.isnan(end):
            end = 1

        # add nstock history
        nstock[sdate] = len(stocks)


########################## 2021.3.6 新增 ##########################################
    aaa = []
    for i in range(len(return_rates)):  # "return_rates" is [list].
        j = i+2
        aa = [sdate_list[j-2], edate_list[j-2], "報酬率：", return_rates[i]]
        # print('--------------aa list---------------')
        # print(aa)
        aaa.append(aa)  # aaa是aa的組成, aaa is table
        # print(aaa)
    df_aaa = pd.DataFrame(
        aaa, columns=["sdate", "edate", "", "報酬率"], dtype="float64")
    # print(df_aaa)
    # print(pd.DataFrame(rows))

    rows.append(col)
    # print('----------row新增col---------')
    # print(rows)
    print('每次換手最大報酬 : %.2f ％' % maxreturn)
    print('每次換手最少報酬 : %.2f ％' % minreturn)
    print(stocks.index)
    dff = pd.DataFrame(stocks)
    dff.reset_index(inplace=True)
    # print(dff)


##############2021.3.8 新增 #####################################################

    import bokeh.io
    import copy
    from bokeh.plotting import figure, output_file, show, output_notebook, save
    from bokeh.resources import INLINE
    from bokeh.models import ColumnDataSource, Rect, HoverTool, Range1d, RangeTool, DateFormatter
    from bokeh.layouts import column, gridplot, row, widgetbox
    from bokeh.embed import file_html
    from bokeh.resources import CDN
    from bokeh.models.widgets import TableColumn, DataTable, Slider
    from bokeh.models.widgets import Panel, Tabs

    def colorful(a):
        a.background_fill_color = "black"
        a.title.text_color = 'white'
        a.border_fill_color = 'black'
        a.outline_line_color = 'white'
        a.xaxis.axis_line_color = 'white'
        a.xaxis.axis_label_text_color = "yellow"
        a.yaxis.axis_label_text_color = "yellow"
        a.xaxis.major_label_text_color = 'white'
        a.yaxis.major_label_text_color = 'white'
        a.xgrid.grid_line_color = 'white'
        a.ygrid.grid_line_color = 'white'
        a.xgrid.grid_line_alpha = 0.5
        a.ygrid.grid_line_alpha = 0.5

    # df_aaa['累加報酬率']
    return_rates_copy = copy.deepcopy(return_rates)
    # return_rates = [float(x) for x in return_rates]

    for i in range(len(return_rates)-1):
        return_rates[i] = return_rates[i]
        return_rates[i+1] = return_rates[i]+return_rates[i+1]
    print(return_rates)
    df_aaa['累加報酬率'] = return_rates

    df_aaa.sdate = pd.to_datetime(df_aaa.sdate)
    df_aaa.edate = pd.to_datetime(df_aaa.edate)
    # print('--------------df_aaa  DataFrame---------------')
    # print(df_aaa)

    p = figure(
        plot_width=800, plot_height=250, x_axis_type="datetime",
        tools=["pan,wheel_zoom,box_zoom,reset,save"])
    colorful(p)
    p.yaxis.axis_label = "報酬率（％）"
    p.xaxis.axis_label = "日期"
    p.line(df_aaa["sdate"], df_aaa["累加報酬率"],
           legend_label="累加報酬率", line_color="orange", line_width=2)
    p.circle(df_aaa["sdate"], df_aaa["累加報酬率"], color="#FF69B4", size=10)
    # show(p)

    sordata = ColumnDataSource(df_aaa)

    columns = [
        TableColumn(field="sdate", title="sdate", formatter=DateFormatter()),
        TableColumn(field="edate", title="edate", formatter=DateFormatter()),
        TableColumn(field="", title=""),
        TableColumn(field="報酬率", title="報酬率"),
        TableColumn(field="累加報酬率", title="累加報酬率"),
    ]

    data_table = DataTable(source=sordata, columns=columns,
                           fit_columns=True, width=800, height=800)
    t1 = Panel(child=data_table, title="報酬率")  # t1 table1
    # print(dff['stock_id'])
    df_stocks = pd.DataFrame(columns=['符合條件的股票代碼'])
    df_stocks["符合條件的股票代碼"] = dff['stock_id']
    print(df_stocks)
    sordata1 = ColumnDataSource(df_stocks)
    columns1 = [TableColumn(field="符合條件的股票代碼", title="符合條件的股票代碼"),
                ]
    data_table1 = DataTable(source=sordata1, columns=columns1,
                            fit_columns=True, width=800, height=800)
    t2 = Panel(child=data_table1, title="符合條件股票")

    tabs = Tabs(tabs=[t1, t2])
    # show(tabs)

    # show(widgetbox([data_table1], sizing_mode='scale_both'))
# save()方法和.show()不能一起用，使用下面兩行要將上面的show()註解掉 ln253,ln267
    output_file("報酬率.html")
    save(obj=column(p, tabs))
    # output_file("報酬率.html")
    # save(obj=column(p, widgetbox(
    # [data_table, data_table1], sizing_mode='scale_both')))

############## #####################################################

    if benchmark is None:
        benchmark = price['0050'][start_date:end_date].iloc[1:]

    # bechmark (thanks to Markk1227)
    ((benchmark/benchmark[0]-1)*100).plot(color=(0.8, 0.8, 0.8))
    plt.ylabel('Return On Investment (%)')
    plt.grid(linestyle='-.')
    plt.show()

    # ((benchmark/benchmark.cummax()-1)*100).plot(legend=True, color=(0.8, 0.8, 0.8))
    # ((equality/equality.cummax()-1)*100).plot(legend=True)
    # plt.ylabel('Dropdown (%)')
    # plt.grid(linestyle='-.')
    # plt.show()

    pd.Series(nstock).plot.bar()
    plt.ylabel('Number of stocks held')

    fig, ax = plt.subplots(figsize=(10, 10))
    # ax.axis('off')
    # ax.axis('tight')

    tb = ax.table(cellText=df_aaa.values,
                  colLabels=df_aaa.columns, bbox=[0, 0, 1, 1])
    tb[0, 0].set_facecolor('#363636')
    tb[0, 1].set_facecolor('#363636')
    tb[0, 2].set_facecolor('#363636')
    tb[0, 3].set_facecolor('#363636')
    tb[0, 0].set_text_props(color='w')
    tb[0, 1].set_text_props(color='w')
    tb.set_fontsize(30)
    plt.show()
    tmpfile = BytesIO()

    fig.savefig(tmpfile, format='png')
    encoded = base64.b64encode(tmpfile.getvalue()).decode('utf-8')

    html = 'Some html head' + \
        '<img src=\'data:image/png;base64,{}\'>'.format(
            encoded) + 'Some more html'

    with open('test.html', 'w') as f:
        f.write(html)

    # html_graph = mpld3.fig_to_html(fig)
    # print(html_graph)
    # Html_file = open("index.html", "w", encoding='utf-8-sig')
    # Html_file.write(html_graph)
    # Html_file.close()

    return equality, transections


def portfolio(stock_list, money, data, lowest_fee=20, discount=0.6, add_cost=10):
    price = data.get('收盤價', 1)
    stock_list = price.iloc[-1][stock_list].transpose()
    print('estimate price according to', price.index[-1])

    print('initial number of stock', len(stock_list))
    while (money / len(stock_list)) < (lowest_fee - add_cost) * 1000 / 1.425 / discount:
        stock_list = stock_list[stock_list != stock_list.max()]
    print('after considering fee', len(stock_list))

    while True:
        invest_amount = (money / len(stock_list))
        ret = np.floor(invest_amount / stock_list / 1000)

        if (ret == 0).any():
            stock_list = stock_list[stock_list != stock_list.max()]
        else:
            break

    print('after considering 1000 share', len(stock_list))

    return ret, (ret * stock_list * 1000).sum()
