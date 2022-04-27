

<ul>
    <li><a href="/tax/render?year=2022">Current Tax Situation</a></li>
    <li><a href="/tax/render?year=2022&estimate=true">Estimate Tax Situation</a></li>
</ul>

<h1>Tax Projections</h1>
<form action="/tax/render" method="get">
    <label>Year</label>
    <input type="text" name="year" required="required" />

    <label>Income</label>
    <input type="text" name="income" required="required" />
    
    <button type="submit">Simulate Taxes</button>
</form>
