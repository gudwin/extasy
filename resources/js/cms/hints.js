var cms;
if (!cms)
{
	alert('cms - library not found!');
} else {
	cms.hints = new Object();
	cms.hints.init = function () {
		// Hide current tooltips 
		jQuery('.help_link div').attr('class','hideToolTip');
		// Bind hide/show events 
		jQuery('#user_form').on(".help_link",'mouseover',cms.hints.show);
		jQuery('#user_form').on(".help_link",'mouseout',cms.hints.hide);
	}
	cms.hints.show = function () {
		var oIterator = this;
		var nLeft = 0;
		var nTop = 0;
		var $aCss = {
			'position':'absolute',
			'left':this.x,
			'top':this.y
		};
		this.offsetLeft
		// создаем 
		jQuery(this).next('div').attr('class','showToolTip').css($aCss);
	}
	cms.hints.hide = function () {
		jQuery(this).next('div').attr('class','hideToolTip');
	}
	
	// добавляем в код на onDomReady
	jQuery(document).ready(cms.hints.init);
}