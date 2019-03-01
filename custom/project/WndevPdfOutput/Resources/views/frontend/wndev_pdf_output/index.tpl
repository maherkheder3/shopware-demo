<title>PDF - {$sArticle.articleName}</title>
<style>
    .product-image{
        width: 35%;
        display: inline-block;
        float: left;
    }
    .product-box{
        width: 65%;
        display: inline-block;
    }
    h3{
       margin-bottom: 15px;
    }

    table{
        margin-bottom: 40px;
    }

    .artikel-number-box tr td:nth-child(2){
        padding-left: 15px;
    }

</style>
<body>

<htmlpageheader name="myHTMLHeader1">
    <table width="700px" class="table_pageheader" style="border-bottom:1px solid #000;">
        <tr>
            <td width="40%" align="right">
                <img style="margin: 10px 40px 40px"
                     src="{$shopLogo}" alt="" >
            </td>
        </tr>
    </table>
</htmlpageheader>
<htmlpagefooter name="myHTMLFooter">

</htmlpagefooter>

<sethtmlpageheader name="myHTMLHeader1" page="O" value="on" show-this-page="1" />
<sethtmlpagefooter name="myHTMLFooter" page="0" value="on" />
<div id="content">

    <h1>{$sArticle.articleName}</h1>
    {if $sArticle.description}
        <p>{$sArticle.description}</p>
    {/if}

    <table cellspacing="0" class="artikel-number-box">
        <tr>
            <td>Artikelnummer:</td>
            <td>{$sArticle.ordernumber}</td>
        </tr>
        <tr>
            <td>Listenpreis:</td>
            <td>{$sArticle.price|currency}</td>
        </tr>
        <tr>
            <td>Lieferung:</td>
            <td>
                {if $sArticle.shippingtime}
                    {$sArticle.shippingtime} weekdays
                {elseif $sBasketItem.shippingtime}
                    {$sBasketItem.shippingtime} weekdays
                {else}Ready for immediate shipment, delivery time approx. 1-3 workdays
                {/if}
            </td>
        </tr>
    </table>

    <div class="product">
        <div class="product-image">
            <table>
                <tr>
                    <td>
                        <img src="{$sArticle.image.source}" width="100%" class="pt-4" alt="">
                    </td>
                </tr>
            </table>
        </div>

        <div class="product-box">
            <ul class="">
                {if $sArticle.additionaltext }<li>{$sArticle.additionaltext} </li>{/if}
                {if $sArticle.ordernumber }<li>{$sArticle.ordernumber} </li>{/if}
                {if $sArticle.suppliernumber }<li>{$sArticle.suppliernumber} </li>{/if}
                {if $sArticle.attr11 }<li>{$sArticle.attr11} </li>{/if}
                {if $sArticle.purchaseunit }<li>{$sArticle.purchaseunit|string_format:"%d"} {$variante.unit} </li>{/if}
                {if $sArticle.price }<li>{$sArticle.price|currency} </li>{/if}
                {if $sArticle.attr12 }<li>{$sArticle.attr12} </li>{/if}
            </ul>
        </div>
    </div>
    <div style="clear: left;"></div>

    <div class="description_long">
        {if $sArticle.description_long}
            <h3>Description</h3>
            <p>{$sArticle.description_long}</p>
        {/if}
    </div>

    <div class="properties">
        <h3>Eigenschaften</h3>
        <table style="width: 100%">
            <thead>
                <tr>
                    <th width="35%" style="text-align: left">Eigenschaft</th>
                    <th width="65%" style="text-align: left">Wert</th>
                </tr>
            </thead>
            <tbody>
                {foreach $sArticle.sProperties as $property}
                    <tr>
                        <td>{$property.name}</td>
                        <td>{$property.value}</td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
    </div>

    <div class="attributes">
        {if $sAttrs}
            <h3>Article Properties</h3>
            <ul>
                {foreach $sAttrs as $attr}
                    <li>{$attr}</li>
                {/foreach}
            </ul>
        {/if}
    </div>

</div>
</body>

