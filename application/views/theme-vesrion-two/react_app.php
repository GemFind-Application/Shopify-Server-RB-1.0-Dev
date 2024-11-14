<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <link rel="icon" type="image/svg+xml" href="/vite.svg" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="theme-color" content="#000" />
    <meta name="description" content="Web site created using Locofy" />
    <script type="text/javascript">
        var input = document.createElement('input');
        input.setAttribute('type', 'hidden');
        input.setAttribute('id', 'shop_domain');
        input.setAttribute('value', Shopify.shop);
        document.body.appendChild(input);
		console.log(Shopify);
    </script>
    <title>RB2.O JV NEW</title>
    <script type="module" crossorigin src="<?php echo base_url()?>public/react/newbuild/assets/main.js"></script>
    <link rel="stylesheet" crossorigin href="<?php echo base_url()?>public/react/newbuild/assets/main.css">
  </head>
  <body>
    <div id="root"></div>

  </body>
</html>

<script>


 /* const data = {
    name: 'test',
    email: '',
    friendname:'friendname',
    frinedemail:'',
    isLabSetting:0,
    settingid:'4750395',
    ringurl:'http://localhost:5173/setting-details/4750395',
    shopurl:'gemfind-product-demo-10.myshopify.com',
    message:'this is message'
};
let stringToPass;
Object.keys(data).forEach(function (key) {
        stringToPass += key + "=" + encodeURIComponent(data[key]) + "&";
      });
      let formData = new FormData();
formData.append('name', data.name);
formData.append('email', data.email); 
formData.append('friend_name', data.friendname);
formData.append('friend_email', data.frinedemail); 
formData.append('isLabSetting', data.isLabSetting);
formData.append('settingid', data.settingid); 
formData.append('ringurl', data.ringurl);
formData.append('shopurl', data.shopurl); 
formData.append('message', data.message); 

const requestOptions = {
  method: 'POST',
  body:formData,
 
};
//const corsProxy = 'https://cors-anywhere.herokuapp.com/';
//const url = 'https://gemfind.us/ringbuilder/ringbuilder/settings/resultemailfriend';


const url = 'https://ringbuilderdev.gemfind.us/ringbuilder/settings/resultemailfriend';
console.log(url);

//const url = '/ringbuilder/settings/resultemailfriend';*/
/*const requestOptions = {
  method: 'POST',
  headers: {
    'Content-type': 'application/json; charset=UTF-8',
  },
  body:JSON.stringify({password:'demo10',settingId:4852733,'isLabSetting':false,shopurl:'gemfind-product-demo-10.myshopify.com'})
 
};
fetch('http://api.jewelcloud.com/api/RingBuilder/AccountAuthentication', requestOptions)
  .then(response => {
    if (!response.ok) {
      throw new Error('Network response was not ok');
    }
    return response.json();
  })
  .then(data => {
    console.log(data)
  })
  .catch(error => {
    console.error

('Error:', error);
  });*/
</script>
