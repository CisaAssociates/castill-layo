<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light py-5">

    <div class="container text-center">
        <div class="card shadow-lg p-4 mb-5 bg-white rounded">
            <h1 class="display-4 text-primary mb-4">Welcome!</h1>

            @if($rfid)
            <h2 class="text-success mb-3">RFID: <span class="font-weight-bold" id="rfid">{{ $rfid }}</span></h2>
            <input type="hidden" id="current-rfid" value="{{ $rfid }}">
            @endif

            @if($photo)
            <div class="mb-3">
                <img src="{{ asset($photo) }}" alt="Captured Photo" id="captured-photo" class="img-fluid rounded shadow-sm" style="max-width: 400px;">
            </div>
            @endif
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>


    <!-- Auto-refresh script -->
    <script>
        setInterval(function() {
            $.ajax({
                url: "{{ route('latest-scan') }}", // Correctly generate the route
                type: "GET",
                dataType: "json", // Ensure the server returns JSON
                success: function(response) {
                    if (response && response.rfid) {
                        var currentRfidElement = document.getElementById("current-rfid");
                        var currentRfid = currentRfidElement ? currentRfidElement.value : null;

                        if (response.rfid !== currentRfid) {
                            console.log('New RFID detected:', response.rfid);
                            location.reload(); // Refresh page if new RFID is detected
                        }
                    } else {
                        console.error('RFID not found in response:', response);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Failed to check for new RFID.');
                    console.error('Status:', status);
                    console.error('Error:', error);
                    console.log('Response Text:', xhr.responseText); // Log response text for debugging
                }
            });
        }, 2000); // Refresh every 2 seconds
    </script>

</body>

</html>