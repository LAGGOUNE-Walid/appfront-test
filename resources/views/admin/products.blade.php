<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Products</title>
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <style>
        .admin-container {
            padding: 20px;
        }

        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .admin-table {
            width: 100%;
            border-collapse: collapse;
        }

        .admin-table th,
        .admin-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .admin-table th {
            background-color: #f2f2f2;
        }

        .admin-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
    </style>
</head>

<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1>Admin - Products</h1>
            <div>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <a href="{{ route('admin.add.product') }}" class="btn btn-primary">Add New Product</a>
                    <button href="{{ route('logout') }}" class="btn btn-secondary" type="submit">Logout</button>
                </form>

            </div>
        </div>

        @if (session('success'))
            <div class="success-message">
                {{ session('success') }}
            </div>
        @endif

        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                    <tr>
                        <td>{{ $product->id }}</td>
                        <td>
                            <img src="{{ url($product->image) }}" width="50" height="50"
                                alt="{{ $product->name }}">
                        </td>
                        <td>{{ $product->name }}</td>
                        <td>${{ number_format($product->price, 2) }}</td>
                        <td>

                            <form action="{{ route('admin.delete.product', $product->id) }}" method="post">
                                <a href="{{ route('admin.edit.product', $product->id) }}"
                                    class="btn btn-primary">Edit</a>
                                @csrf
                                <input type="hidden" name="_method" value="delete" />
                                <button type="submit" class="btn btn-secondary"
                                    onclick="return confirm('Are you sure you want to delete this product?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                {{-- todo: modify pagination style --}}
                {{ $products->links() }}
            </tbody>
        </table>
    </div>
</body>

</html>
