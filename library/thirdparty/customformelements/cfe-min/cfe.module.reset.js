cfe.module.reset=new Class({Extends:cfe.module.submit,type:"Reset",selector:"input[type=reset]",createOriginal:function(){return new Element("input",{type:"reset"})},setupOriginal:function(){this.parent();this.o.addEvent("click",this.notifyReset.bind(this))},notifyReset:function(){(function(){$A(this.o.form.getElements("input, textarea, select")).each(function(a){a.fireEvent("change")})}).delay(40,this)}});