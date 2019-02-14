<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.2.1/css/bootstrap-grid.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-table/1.13.4/bootstrap-table.min.css">

    <title>PDF - {$sArticle.articleName}</title>
    <style>
        body {
            /*width: 803px;   !* 2324px *!*/
            width: 210mm;
            height: 297mm;
            margin: 0 auto;
            background-color: #006d00;
        }
        .container{
            background-color: white;
            padding: 0 30px;
        }
        .article-img img{
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <img style="float: right; margin: 10px 40px 40px"
                src="http://shopware.l/themes/Frontend/Responsive/frontend/_public/src/img/logos/logo--mobile.png" alt="">
        </div>
        <div style="clear:right"></div>
        <div class="row">
            <div class="col-sm-12">
                <h3>{$sArticle.articleName}</h3>
                <p>{$sArticle.description}</p>
            </div>
            <div class="col-sm-4">
                <strong>Artikelnummer</strong>
            </div>
            <div class="col-sm-8">
                <strong>{$sArticle.ordernumber}</strong>
            </div>
            <div class="col-sm-4">
                <strong>Listenpreis</strong>
            </div>
            <div class="col-sm-8">
                <strong>{$sArticle.price|currency}</strong>
            </div>

            <div class="col-sm-4">
                <img src="{$sArticle.image.source}" width="100%" class="pt-4" alt="">
            </div>

            <div class="col-sm-8">
                <ul class="list-group pt-4">
                    {if $sArticle.additionaltext }<li>{$sArticle.additionaltext} {/if}</li>
                    {if $sArticle.ordernumber }<li>{$sArticle.ordernumber} {/if}</li>
                    {if $sArticle.suppliernumber }<li>{$sArticle.suppliernumber} {/if}</li>
                    {if $sArticle.attr11 }<li>{$sArticle.attr11} {/if}</li>
                    {if $sArticle.purchaseunit }<li>{$sArticle.purchaseunit|string_format:"%d"} {$variante.unit} {/if}</li>
                    {if $sArticle.price }<li>{$sArticle.price|currency} {/if}</li>
                    {if $sArticle.attr12 }<li>{$sArticle.attr12} {/if}</li>
                </ul>
            </div>
        </div>
        <div class="">
            {foreach $sArticle.sProperties as $property}
                <h4>{$property.name}</h4>
                <div class="row">
                    <div class="col-sm-4">
                        {$property.groupName}
                    </div>
                    <div class="col-sm-8">
                        {$property.value}
                    </div>
                </div>
            {/foreach}
            <div class="col-sm-4"></div>
        </div>
    </div>
</body>
</html>

