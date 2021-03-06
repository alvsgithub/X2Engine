<?php
/*****************************************************************************************
 * X2CRM Open Source Edition is a customer relationship management program developed by
 * X2Engine, Inc. Copyright (C) 2011-2013 X2Engine Inc.
 * 
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU Affero General Public License version 3 as published by the
 * Free Software Foundation with the addition of the following permission added
 * to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED WORK
 * IN WHICH THE COPYRIGHT IS OWNED BY X2ENGINE, X2ENGINE DISCLAIMS THE WARRANTY
 * OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU Affero General Public License for more
 * details.
 * 
 * You should have received a copy of the GNU Affero General Public License along with
 * this program; if not, see http://www.gnu.org/licenses or write to the Free
 * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301 USA.
 * 
 * You can contact X2Engine, Inc. P.O. Box 66752, Scotts Valley,
 * California 95067, USA. or at email address contact@x2engine.com.
 * 
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU Affero General Public License version 3.
 * 
 * In accordance with Section 7(b) of the GNU Affero General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "Powered by
 * X2Engine" logo. If the display of the logo is not reasonably feasible for
 * technical reasons, the Appropriate Legal Notices must display the words
 * "Powered by X2Engine".
 *****************************************************************************************/

$this->pageTitle = Yii::t('marketing','Campaigns');
$this->actionMenu = $this->formatMenu(array(
	array('label'=>Yii::t('marketing','All Campaigns')),
	array('label'=>Yii::t('marketing','Create Campaign'), 'url'=>array('create')),
	array('label'=>Yii::t('contacts','Contact Lists'), 'url'=>array('/contacts/lists')),
	array(
        'label'=>Yii::t('marketing','Newsletters'), 
        'url'=>array('/marketing/weblist/index'),
        'visible'=>(Yii::app()->params->edition==='pro')
    ),
	array('label'=>Yii::t('marketing','Web Lead Form'), 'url'=>array('webleadForm')),
	array(
        'label'=>Yii::t('marketing','Web Tracker'), 
        'url'=>array('webTracker'),
        'visible'=>(Yii::app()->params->edition==='pro')
    ),
	array(
        'label'=>Yii::t('app','X2Flow'),
        'url'=>array('/studio/flowIndex'),
        'visible'=>(Yii::app()->params->edition==='pro')
    ),
));

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('marketing-grid', {
		data: $(this).serialize()
	});
	return false;
});
");

function trimText($text) {
	if(mb_strlen($text,'UTF-8')>150)
		return mb_substr($text,0,147,'UTF-8').'...';
	else
		return $text;
}
?>

<?php
foreach(Yii::app()->user->getFlashes() as $key => $message) {
	echo '<div class="flash-' . $key . '">' . $message . "</div>\n";
} ?>

<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->
<?php

$this->widget('application.components.X2GridView', array(
	'id'=>'marketing-grid',
	'baseScriptUrl'=>Yii::app()->request->baseUrl.'/themes/'.Yii::app()->theme->name.'/css/gridview',
	'title'=>Yii::t('marketing','Campaigns'),
	'buttons'=>array('advancedSearch','clearFilters','columnSelector','autoResize'),
	'template'=> 
        '<div id="x2-gridview-top-bar-outer" class="x2-gridview-fixed-top-bar-outer">'.
        '<div id="x2-gridview-top-bar-inner" class="x2-gridview-fixed-top-bar-inner">'.
        '<div id="x2-gridview-page-title" '.
         'class="page-title icon marketing x2-gridview-fixed-title">'.
        '{title}{buttons}{filterHint}'.
        
        '{summary}{items}{pager}',
    'fixedHeader'=>true,
	'dataProvider'=>$model->search(),
	// 'enableSorting'=>false,
	// 'model'=>$model,
	'filter'=>$model,
	// 'columns'=>$columns,
	'modelName'=>'Campaign',
	'viewName'=>'campaigns',
	// 'columnSelectorId'=>'contacts-column-selector',
	'defaultGvSettings'=>array(
        'gvCheckbox' => 30,
		'name' => 156,
		'listId' => 106,
		'subject' => 271,
		'launchDate' => 76,
		'active' => 44,
		'lastUpdated' => 78,
	),
	'specialColumns'=>array(
		'name'=>array(
			'name'=>'name',
			'value'=>'CHtml::link($data->name,array("view","id"=>$data->id))',
			'type'=>'raw',
		),
		'description'=>array(
			'name'=>'description',
			'header'=>Yii::t('marketing','Description'),
			'value'=>'trimText($data->description)',
			'type'=>'raw',
		),
	),
	'enableControls'=>true,
	'fullscreen'=>true,
));
 ?>
