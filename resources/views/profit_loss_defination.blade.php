<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>

    {{-- FOR BOOTSTRAP  --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
</head>

<body>
    <div class="container">
        <u>
            <h3 class="text-center mt-3 mb-4">
                ProfitLossDefinition
                <small class="text-muted">-- DRC</small>
            </h3>
        </u>
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Month/Year</th>
                    <th colspan='3' class="text-center">Buy</th>
                    <th colspan='3' class="text-center">Sell</th>
                    <th class="text-center">Remain Stock</th>
                    <th class="text-center">Profit/Loss</th>
                </tr>
                <tr>
                    <th></th>
                    <th>Qty</th>
                    <th>Rate</th>
                    <th>Total</th>
                    <th>Qty</th>
                    <th>Rate</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($modified_array as $key => $value)
                <tr>
                    <td>{{ $value['Month/Year'] }}</td>
                    <td>{{ isset($value['buyQty']) ? $value['buyQty'] : '-' }}</td>
                    <td>{{ isset($value['buyRate']) ? $value['buyRate'] : '-' }}</td>
                    <td>{{ isset($value['buyTotal']) ? $value['buyTotal'] : '-' }}</td>
                    <td>{{ $value['sellQty'] }}</td>
                    <td>{{ $value['sellRate'] }}</td>
                    <td>{{ $value['sellTotal'] }}</td>
                    <td class="text-end">{{ $value['remaining_stock'] }}</td>
                    <td class="text-end">{{ $value['PTL'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>

</html>