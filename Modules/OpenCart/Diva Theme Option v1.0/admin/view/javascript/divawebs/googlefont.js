$(document).ready(function () {
   $.getJSON('https://www.googleapis.com/webfonts/v1/webfonts?key=AIzaSyCKhdPP0mANQLtEY8Br0H71OqpQHxfu8wo', function (response) {
       console.log(response.items);
   })
});