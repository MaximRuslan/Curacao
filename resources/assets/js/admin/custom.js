/*
|------------------------------------------------------------
| LOADER
|------------------------------------------------------------
*/



var fullLoader = {
  el:{
    loaderElement:'ss_full_loader',
    loaderText:'ssfl_text',
  },
  data:{
    loaderText:'Please Wait'
  },
  init:function(){
    this.bindUIActions();
  },
  bindUIActions:function(){
  },
  on:function(settings){
    var _this = this;
    $('.'+_this.el.loaderElement).addClass('open');
    if(settings){
      if(settings.text){
        $('.'+_this.el.loaderText).text(settings.text);
      }
    }
    else{
      $('.'+_this.el.loaderText).text(_this.data.loaderText);
    }
  },
  off:function(){
    var _this = this;
    $('.'+_this.el.loaderElement).removeClass('open');
  }
}
