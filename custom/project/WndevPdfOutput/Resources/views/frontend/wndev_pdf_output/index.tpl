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
    .properties{
        padding-top: 40px;
    }

</style>
<body>

<htmlpageheader name="myHTMLHeader1">
    <table width="700px" class="table_pageheader" style="border-bottom:1px solid #000;">
        <tr>
            <td width="40%" align="right">
                <img style="margin: 10px 40px 40px"
                     src="http://shopware.l/themes/Frontend/Responsive/frontend/_public/src/img/logos/logo--mobile.png" alt="" >
            </td>
        </tr>
    </table>
</htmlpageheader>
<htmlpagefooter name="myHTMLFooter">
    <h2>test</h2>
</htmlpagefooter>

<sethtmlpageheader name="myHTMLHeader1" page="O" value="on" show-this-page="1" />
<sethtmlpagefooter name="myHTMLFooter" page="0" value="on" />
<div id="content">

    <h1>{$sArticle.articleName}</h1>
    <p>{$sArticle.description}</p>

    <table cellspacing="0">
        <tr>
            <td>Artikelnummer : </td>
            <td>{$sArticle.ordernumber}</td>
        </tr>
        <tr>
            <td>Listenpreis : </td>
            <td>{$sArticle.price|currency}</td>
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

    <div class="properties">
        <table>
            <thead>
                <tr>
                    <th>Eigenschaft</th>
                    <th>Wert</th>
                </tr>
            </thead>
            <tbody>
                {foreach $sArticle.sProperties as $property}
                    <tr>
                        <td>{$property.name}</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>{$property.groupName}</td>
                        <td>{$property.value}</td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
    </div>
</div>
</body>

