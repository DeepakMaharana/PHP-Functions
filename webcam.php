<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <video src="" id="videoTag"></video>
    <button id="cameraBtn">Open Camera</button>
    <script>

        camera_button =  document.getElementById("cameraBtn");
        video =  document.getElementById("videoTag");

        camera_button.addEventListener('click', async function() {
            let stream = await navigator.mediaDevices.getUserMedia({ video: true, audio: false });
            video.srcObject = stream;
        });
    </script>
</body> 
</html>
