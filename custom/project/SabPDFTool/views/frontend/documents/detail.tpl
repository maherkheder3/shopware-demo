{block name='frontend_detail_actions_review' append}
	<li class="link--list-item">
		<a  href="{url module=frontend controller=SabPDFTool action=pdfdetail}?articleID={$sArticle.articleID}"
			target="_blank" title="{s name="ButtonDetailPDFTitle" namespace="plugins/frontend/SabPDFToolv5"}{/s}" class="action--link link--pdf-tool">
			<i class="icon--download"></i>
			{s name="ButtonDetailPDFLink" namespace="plugins/frontend/SabPDFToolv5"}PDF Produktinformationen{/s}
		</a>
	</li>
{/block}
