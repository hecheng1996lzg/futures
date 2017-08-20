<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <script src="{{ asset('assets/script/jquery-3.1.1.js') }}"></script>
    <script src="{{ asset('assets/script/Calculation.js') }}"></script>
    <script src="{{ asset('assets/script/Futures.js') }}"></script>
</head>
<body>

<main>
    <article>
        <form id="form-futures" method="post" enctype="multipart/form-data" action="{{ asset('count/index') }}">
            <p>
                <label for="min_year">开始年份：</label>
                <input type="text" id="min_year" name="min_year" value="2012">
            </p>
            <p>
                <label for="max_year">结束年份：</label>
                <input type="text" id="max_year" name="max_year" value="2017">
            </p>
            <p>
                <select name="comparative_type">
                    <option value="1">默认: 均线对比均线</option>
                    <option value="2">当日均线比前一日均线</option>
                    <option value="3">当日价格比前一日价格</option>
                </select>
            </p>
            <input type="file" id="file" name="fileText">
            <button type="submit">submit</button>
        </form>
    </article>
    <article class="results">

    </article>
</main>

</body>
</html>