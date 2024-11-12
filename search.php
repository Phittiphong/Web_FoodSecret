<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .search-results {
            max-height: 200px;
            overflow-y: auto;
        }
    </style>
</head>

<body>
    <header class="p-3 mb-3 border-bottom">
        <div class="container">
            <!-- Your header content -->
        </div>
    </header>

    <div class="container mt-4">
        <h2>Search</h2>
        <input type="text" id="search" class="form-control" placeholder="Search...">
        <div id="results" class="search-results mt-2"></div>
    </div>

    <footer class="py-5">
        <!-- Your footer content -->
    </footer>

    <script>
        document.getElementById('search').addEventListener('input', function () {
            const query = this.value;

            if (query.length > 2) { // Only start searching if the input length is more than 2 characters
                fetch('search_results.php?query=' + encodeURIComponent(query))
                    .then(response => response.text())
                    .then(data => {
                        document.getElementById('results').innerHTML = data;
                    });
            } else {
                document.getElementById('results').innerHTML = '';
            }
        });
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>
</body>

</html>
