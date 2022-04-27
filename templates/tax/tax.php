

<ul>
    <li><a href="/tax/render?year=2022">Current Tax Situation</a></li>
    <li><a href="/tax/render?year=2022&estimate=true">Estimate Tax Situation</a></li>
</ul>

<h1>Tax Projections</h1>
<form action="/tax/render" method="get">
    <label>Year</label>
    <input type="number" name="year" required="required" value="<?php echo date('Y'); ?>" />

    <label>Income</label>
    <input type="text" name="income" required="required" placeholder="250000" />
    
    <input type="hidden" name="estimate" value="true" />
    <button type="submit">Simulate Taxes</button>
</form>
