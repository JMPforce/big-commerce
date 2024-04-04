const express = require('express');
const ngrok = require('ngrok')
const app = express();
 
// when there's a post request to /webhooks...
app.post('/webhooks', function (req, res) {
 
    // respond with 200 OK
    res.send('OK');
});
 
// listen to port 3000
app.listen(3000, function () {
    console.log('Listening for webhooks on port 3000');
 
    // start ngrok and create a tunnel to port 3000
    (async function() {
        const url = await ngrok.connect(3000);
    })();
})