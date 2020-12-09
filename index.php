<?php
$node = '01';
if(isset($_GET['node']))
    $node = $_GET['node'];
?>
<!doctype html>
<html lang="en">
<head>
<title>Sarebide</title>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
</head>
<body>
<h1>Sarebide Blockchain</h1>
<hr>
<h2>Node: <?=$node;?></h2>
<hr>
<h3>Stage / Checkpoint files:</h3>
<div>
    <form action="save.php?node=<?=$node?>" method="post" enctype="multipart/form-data">
        <input type="file" name="files[]" multiple="multiple">
        <input type="submit">
    </form>
</div>
<h3>Search ID: </h3>
<div>
    <form action="search.php" method="get">
        <input name="q" type="text">
        <input name="node" type="hidden" value="<?=$node?>">
        <input type="submit">        
    </form>
</div>
<h3>Generated certifies: </h3>
<div>
    <ul>
    </ul>
</div>
<script>
(function()
{
    window.setInterval(function()
    {
        uploadCert();
    }, 5 * 1000);

    function uploadCert()
    {
        fetch('http://zital-pi.no-ip.org/sarebide/certified.php')
        .then(function(response)
        {
            return response.json();
        })
        .then(function(response)
        {
            let ul = document.querySelector('ul');
            while(ul.hasChildNodes())
                ul.removeChild(ul.firstChild);

            response.map(function(cert)
            {
                let a = document.createElement('a');
                a.href = "http://zital-pi.no-ip.org/sarebide/certified/"+cert;
                a.appendChild(document.createTextNode(cert));
                let li = document.createElement('li');
                li.appendChild(a);
                ul.appendChild(li);
            }); 
        });
    }
    uploadCert();
})();
</script>
</body>
</html>