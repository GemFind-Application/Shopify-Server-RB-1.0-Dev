<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="{{ asset('favicon.jpg') }}" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>RingBuilder Advance</title>
    <script type="text/javascript">
        var input = document.createElement('input');
        input.setAttribute('type', 'hidden');
        input.setAttribute('id', 'shop_domain');
        input.setAttribute('value', Shopify.shop);
        document.body.appendChild(input);
		console.log(Shopify);
    </script>
    <title>React App</title>
    <link rel="stylesheet" href="<?php echo base_url()?>public/react/build/static/css/main.css">

</head>
<body>
    <div id="root"></div>
   
    <script src="<?php echo base_url()?>public/react/build/static/js/main.js"></script>
     <span class="gemfind_powered_by" id="gemfind_diamondtool_powered_by"
        style="text-align: right; display: none; margin-right: 7%; color: #000">
        <a href="https://gemfind.com/" target="_blank" rel="nofollow"
            style="
          color: inherit;
          text-decoration: none;
          font-family: 'Lato', sans-serif;
        ">
            Powered By GemFind
        </a>
    </span>
</body>
</html>
