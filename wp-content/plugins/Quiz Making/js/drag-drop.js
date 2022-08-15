jQuery(document).ready(function(){
    var r = []
      , a = function t(e, i) {
        var n = i.position().left + "px"
          , s = i.position().top + "px";
        e.css({
            top: parseInt(s),
            left: parseInt(n)
        })
    };
     
    jQuery(document).find('.dragable').pep({
        droppable: '.hotspotAnswer',
        overlapFunction: false,
        useCSSTranslation: false,
        start: function t(ev, obj){
            obj.noCenter = false;
        },
        drag: function t(ev, obj){
          var vel = obj.velocity();
          var rot = (vel.x)/5;
          rotate(obj.$el, rot)    
        },
        stop: function t(ev, obj){
           var n = this, s = this.activeDropRegions[0];
          rotate(obj.$el, 0)         
        },
        rest:function t(ev,obj){
            var n = this, s = this.activeDropRegions[0];
            var top = n.$el.css('top');
            var left = n.$el.css('left');
            var pos = 't-'+top+'_l-'+left;
            var option = n.$el.data('option');
            jQuery(n.$el).parents('.inner-outer-repeater').find('.target'+option).attr('data-left',left);
            jQuery(n.$el).parents('.inner-outer-repeater').find('.target'+option).attr('data-top',top);
            jQuery(n.$el).parents('.inner-outer-repeater').find('.target'+option).val(pos);
            

        },
        revert: !0,
        revertIf: function t(e, i) {
                  return !this.activeDropRegions.length
        },
      }); 
});


function rotate($obj, deg){
  $obj.css({ 
      "-webkit-transform": "rotate("+ deg +"deg)",
         "-moz-transform": "rotate("+ deg +"deg)",
          "-ms-transform": "rotate("+ deg +"deg)",
           "-o-transform": "rotate("+ deg +"deg)",
              "transform": "rotate("+ deg +"deg)" 
    }); 
}
