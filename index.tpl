<!DOCTYPE html>
<html>
<head>
    <title>The normal homepage</title>
    <script src="/Berrie/Nytrix/data/jquery-1.11.3.min.js"></script>
    <script>
    $(document).ready(function(){
        $("#otherpage").click(function(){
            $.ajax("/Cleverload/hey").done(function(data){
                document.write(data);
            });
        });
    });
    </script>
</head>
<body>
    Infront <br>
    <include>test.php</include>
    <br>After <br>
</body>
</html>