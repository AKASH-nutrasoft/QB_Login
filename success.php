<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Error</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css">
    <br>
</head>
<body>
<div class="row">
  <div class="col-md-6 text-center">
    <div class="card">
        <p class="text-success"><i class="fa fa-check-circle fa-5x"></i></p>
        <p class="message">Connected Successfully!</p>
        <p class="text">You are now connected to QuickBooks.</p>
        <a href="/" class="btn btn-primary">Back to Dashboard</a>
    </div>
  </div>
</div>
</body>
</html>

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        overflow: hidden;
    }

    .card {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        border-radius: 20px;
        box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
        transition: 0.3s;
        padding: 50px 100px 100px 100px;
        background-color: #fff;
    }

    .card:hover {
        box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2);
    }

    .message {
        font-size: 22px; 
        font-weight: 800;
        color: #d9534f;
    }

    .text{
        font-size: 15px; 
        font-weight: 550;
        color: #333;
    }
</style>
