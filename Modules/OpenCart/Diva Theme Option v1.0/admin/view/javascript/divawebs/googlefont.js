$(document).ready(function () {
    $.getJSON('https://www.googleapis.com/webfonts/v1/webfonts?key=AIzaSyCKhdPP0mANQLtEY8Br0H71OqpQHxfu8wo', function (response) {
        var fonts = [];
        for (i = 0; i < response.items.length; i++) {
            fonts[i] = {
               'family' : response.items[i]['family'],
               'variants' : response.items[i]['variants'],
               'subsets': response.items[i]['subsets']
            };
        }

        var options_html = '';
        var variants_html = '';
        var subsets_html = '';
        for (i = 0; i < fonts.length; i++) {
            var font_val = fonts[i]['family'].replace(' ', '+');
            options_html += "<option value='" + font_val + "'>" + fonts[i]['family'] +"</option>";
        }

        $('.font-box').html(options_html);
   })
});