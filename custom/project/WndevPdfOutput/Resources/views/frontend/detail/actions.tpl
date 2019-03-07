{extends file='parent:frontend/detail/actions.tpl'}

{block name='frontend_detail_actions_review'}
    {$smarty.block.parent}

    <a href="{url controller='WndevPdfOutput' articleId=$sArticle.articleID}" class="action--link">
        <i class="icon--star"></i> PDF
    </a>
{/block}
