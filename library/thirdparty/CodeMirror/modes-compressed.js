CodeMirror.defineMode("xml",function(v,k){var p=v.indentUnit;var u=k.htmlMode?{autoSelfClosers:{br:true,img:true,hr:true,link:true,input:true,meta:true,col:true,frame:true,base:true,area:true},doNotIndent:{pre:true,"!cdata":true},allowUnquoted:true}:{autoSelfClosers:{},doNotIndent:{"!cdata":true},allowUnquoted:false};var a=k.alignCDATA;var f,g;function o(z,y){function w(B){y.tokenize=B;return B(z,y);}var x=z.next();if(x=="<"){if(z.eat("!")){if(z.eat("[")){if(z.match("[CDATA[")){return w(t("xml-cdata","]]>"));}else{return null;}}else{if(z.match("--")){return w(t("xml-comment","-->"));}else{if(z.match("DOCTYPE")){z.eatWhile(/[\w\._\-]/);return w(t("xml-doctype",">"));}else{return null;}}}}else{if(z.eat("?")){z.eatWhile(/[\w\._\-]/);y.tokenize=t("xml-processing","?>");return"xml-processing";}else{g=z.eat("/")?"closeTag":"openTag";z.eatSpace();f="";var A;while((A=z.eat(/[^\s\u00a0=<>\"\'\/?]/))){f+=A;}y.tokenize=n;return"xml-tag";}}}else{if(x=="&"){z.eatWhile(/[^;]/);z.eat(";");return"xml-entity";}else{z.eatWhile(/[^&<]/);return null;}}}function n(y,x){var w=y.next();if(w==">"||(w=="/"&&y.eat(">"))){x.tokenize=o;g=w==">"?"endTag":"selfcloseTag";return"xml-tag";}else{if(w=="="){g="equals";return null;}else{if(/[\'\"]/.test(w)){x.tokenize=j(w);return x.tokenize(y,x);}else{y.eatWhile(/[^\s\u00a0=<>\"\'\/?]/);return"xml-word";}}}}function j(w){return function(y,x){while(!y.eol()){if(y.next()==w){x.tokenize=n;break;}}return"xml-attribute";};}function t(x,w){return function(z,y){while(!z.eol()){if(z.match(w)){y.tokenize=o;break;}z.next();}return x;};}var l,h;function b(){for(var w=arguments.length-1;w>=0;w--){l.cc.push(arguments[w]);}}function e(){b.apply(null,arguments);return true;}function i(w,y){var x=u.doNotIndent.hasOwnProperty(w)||(l.context&&l.context.noIndent);l.context={prev:l.context,tagName:w,indent:l.indented,startOfLine:y,noIndent:x};}function r(){if(l.context){l.context=l.context.prev;}}function d(w){if(w=="openTag"){l.tagName=f;return e(m,c(l.startOfLine));}else{if(w=="closeTag"){r();return e(q);}else{if(w=="xml-cdata"){if(!l.context||l.context.name!="!cdata"){i("!cdata");}if(l.tokenize==o){r();}return e();}else{return e();}}}}function c(w){return function(x){if(x=="selfcloseTag"||(x=="endTag"&&u.autoSelfClosers.hasOwnProperty(l.tagName.toLowerCase()))){return e();}if(x=="endTag"){i(l.tagName,w);return e();}return e();};}function q(w){if(w=="endTag"){return e();}return b();}function m(w){if(w=="xml-word"){h="xml-attname";return e(m);}if(w=="equals"){return e(s,m);}return b();}function s(w){if(w=="xml-word"&&u.allowUnquoted){h="xml-attribute";return e();}if(w=="xml-attribute"){return e();}return b();}return{startState:function(){return{tokenize:o,cc:[],indented:0,startOfLine:true,tagName:null,context:null};},token:function(z,y){if(z.sol()){y.startOfLine=true;y.indented=z.indentation();}if(z.eatSpace()){return null;}h=g=f=null;var x=y.tokenize(z,y);if((x||g)&&x!="xml-comment"){l=y;while(true){var w=y.cc.pop()||d;if(w(g||x)){break;}}}y.startOfLine=false;return h||x;},indent:function(y,w){var x=y.context;if(x&&x.noIndent){return 0;}if(a&&/<!\[CDATA\[/.test(w)){return 0;}if(x&&/^<\//.test(w)){x=x.prev;}while(x&&!x.startOfLine){x=x.prev;}if(x){return x.indent+p;}else{return 0;}},electricChars:"/"};});CodeMirror.defineMIME("application/xml","xml");CodeMirror.defineMIME("text/html",{name:"xml",htmlMode:true});CodeMirror.defineMode("css",function(e){var d=e.indentUnit,f;function c(i,j){f=j;return i;}function h(k,j){var i=k.next();if(i=="@"){k.eatWhile(/\w/);return c("css-at",k.current());}else{if(i=="/"&&k.eat("*")){j.tokenize=a;return a(k,j);}else{if(i=="<"&&k.eat("!")){j.tokenize=b;return b(k,j);}else{if(i=="="){c(null,"compare");}else{if((i=="~"||i=="|")&&k.eat("=")){return c(null,"compare");}else{if(i=='"'||i=="'"){j.tokenize=g(i);return j.tokenize(k,j);}else{if(i=="#"){k.eatWhile(/\w/);return c("css-selector","hash");}else{if(i=="!"){k.match(/^\s*\w*/);return c("css-important","important");}else{if(/\d/.test(i)){k.eatWhile(/[\w.%]/);return c("css-unit","unit");}else{if(/[,.+>*\/]/.test(i)){return c(null,"select-op");}else{if(/[;{}:\[\]]/.test(i)){return c(null,i);}else{k.eatWhile(/[\w\\\-_]/);return c("css-identifier","identifier");}}}}}}}}}}}}function a(l,k){var i=false,j;while((j=l.next())!=null){if(i&&j=="/"){k.tokenize=h;break;}i=(j=="*");}return c("css-comment","comment");}function b(l,k){var j=0,i;while((i=l.next())!=null){if(j>=2&&i==">"){k.tokenize=h;break;}j=(i=="-")?j+1:0;}return c("css-comment","comment");}function g(i){return function(m,k){var l=false,j;while((j=m.next())!=null){if(j==i&&!l){break;}l=!l&&j=="\\";}if(!l){k.tokenize=h;}return c("css-string","string");};}return{startState:function(i){return{tokenize:h,baseIndent:i||0,stack:[]};},token:function(l,k){if(l.eatSpace()){return null;}var j=k.tokenize(l,k);var i=k.stack[k.stack.length-1];if(f=="hash"&&i=="rule"){j="css-colorcode";}else{if(j=="css-identifier"){if(i=="rule"){j="css-value";}else{if(!i||i=="@media{"){j="css-selector";}}}}if(i=="rule"&&/^[\{\};]$/.test(f)){k.stack.pop();}if(f=="{"){if(i=="@media"){k.stack[k.stack.length-1]="@media{";}else{k.stack.push("{");}}else{if(f=="}"){k.stack.pop();}else{if(f=="@media"){k.stack.push("@media");}else{if(i!="rule"&&i!="@media"&&f!="comment"){k.stack.push("rule");}}}}return j;},indent:function(j,i){var k=j.stack.length;if(/^\}/.test(i)){k-=j.stack[j.stack.length-1]=="rule"?2:1;}return j.baseIndent+k*d;},electricChars:"}"};});CodeMirror.defineMIME("text/css","css");CodeMirror.defineMode("javascript",function(H,L){var v=H.indentUnit;var P=L.json;var b=function(){function V(Y){return{type:Y,style:"js-keyword"};}var S=V("keyword a"),X=V("keyword b"),W=V("keyword c");var T=V("operator"),U={type:"atom",style:"js-atom"};return{"if":S,"while":S,"with":S,"else":X,"do":X,"try":X,"finally":X,"return":W,"break":W,"continue":W,"new":W,"delete":W,"throw":W,"var":V("var"),"function":V("function"),"catch":V("catch"),"for":V("for"),"switch":V("switch"),"case":V("case"),"default":V("default"),"in":T,"typeof":T,"instanceof":T,"true":U,"false":U,"null":U,"undefined":U,"NaN":U,"Infinity":U};}();var M=/[+\-*&%=<>!?|]/;function Q(U,T,S){T.tokenize=S;return S(U,T);}function h(V,S){var U=false,T;while((T=V.next())!=null){if(T==S&&!U){return false;}U=!U&&T=="\\";}return U;}var R,p;function A(U,T,S){R=U;p=S;return T;}function l(W,U){var S=W.next();if(S=='"'||S=="'"){return Q(W,U,z(S));}else{if(/[\[\]{}\(\),;\:\.]/.test(S)){return A(S);}else{if(S=="0"&&W.eat(/x/i)){W.eatWhile(/[\da-f]/i);return A("number","js-atom");}else{if(/\d/.test(S)){W.match(/^\d*(?:\.\d*)?(?:e[+\-]?\d+)?/);return A("number","js-atom");}else{if(S=="/"){if(W.eat("*")){return Q(W,U,f);}else{if(W.eat("/")){W.skipToEnd();return A("comment","js-comment");}else{if(U.reAllowed){h(W,"/");W.eatWhile(/[gimy]/);return A("regexp","js-string");}else{W.eatWhile(M);return A("operator",null,W.current());}}}}else{if(M.test(S)){W.eatWhile(M);return A("operator",null,W.current());}else{W.eatWhile(/[\w\$_]/);var V=W.current(),T=b.propertyIsEnumerable(V)&&b[V];return T?A(T.type,T.style,V):A("variable","js-variable",V);}}}}}}}function z(S){return function(U,T){if(!h(U,S)){T.tokenize=l;}return A("string","js-string");};}function f(V,U){var S=false,T;while(T=V.next()){if(T=="/"&&S){U.tokenize=l;break;}S=(T=="*");}return A("comment","js-comment");}var k={atom:true,number:true,variable:true,string:true,regexp:true};function t(X,T,S,W,U,V){this.indented=X;this.column=T;this.type=S;this.prev=U;this.info=V;if(W!=null){this.align=W;}}function w(U,T){for(var S=U.localVars;S;S=S.next){if(S.name==T){return true;}}}function D(W,T,S,V,X){var Y=W.cc;u.state=W;u.stream=X;u.marked=null,u.cc=Y;if(!W.lexical.hasOwnProperty("align")){W.lexical.align=true;}while(true){var U=Y.length?Y.pop():P?x:y;if(U(S,V)){while(Y.length&&Y[Y.length-1].lex){Y.pop()();}if(u.marked){return u.marked;}if(S=="variable"&&w(W,V)){return"js-localvariable";}return T;}}}var u={state:null,column:null,marked:null,cc:null};function a(){for(var S=arguments.length-1;S>=0;S--){u.cc.push(arguments[S]);}}function F(){a.apply(null,arguments);return true;}function m(T){var U=u.state;if(U.context){u.marked="js-variabledef";for(var S=U.localVars;S;S=S.next){if(S.name==T){return;}}U.localVars={name:T,next:U.localVars};}}var C={name:"this",next:{name:"arguments"}};function s(){if(!u.state.context){u.state.localVars=C;}u.state.context={prev:u.state.context,vars:u.state.localVars};}function r(){u.state.localVars=u.state.context.vars;u.state.context=u.state.context.prev;}function j(T,U){var S=function(){var V=u.state;V.lexical=new t(V.indented,u.stream.column(),T,null,V.lexical,U);};S.lex=true;return S;}function E(){var S=u.state;if(S.lexical.prev){if(S.lexical.type==")"){S.indented=S.lexical.indented;}S.lexical=S.lexical.prev;}}E.lex=true;function c(T){return function S(U){if(U==T){return F();}else{if(T==";"){return a();}else{return F(arguments.callee);}}};}function y(S){if(S=="var"){return F(j("vardef"),I,c(";"),E);}if(S=="keyword a"){return F(j("form"),x,y,E);}if(S=="keyword b"){return F(j("form"),y,E);}if(S=="{"){return F(j("}"),n,E);}if(S==";"){return F();}if(S=="function"){return F(i);}if(S=="for"){return F(j("form"),c("("),j(")"),g,c(")"),E,y,E);}if(S=="variable"){return F(j("stat"),B);}if(S=="switch"){return F(j("form"),x,j("}","switch"),c("{"),n,E,E);}if(S=="case"){return F(x,c(":"));}if(S=="default"){return F(c(":"));}if(S=="catch"){return F(j("form"),s,c("("),q,c(")"),y,E,r);}return a(j("stat"),x,c(";"),E);}function x(S){if(k.hasOwnProperty(S)){return F(K);}if(S=="function"){return F(i);}if(S=="keyword c"){return F(x);}if(S=="("){return F(j(")"),x,c(")"),E,K);}if(S=="operator"){return F(x);}if(S=="["){return F(j("]"),N(x,"]"),E,K);}if(S=="{"){return F(j("}"),N(o,"}"),E,K);}return F();}function K(S,T){if(S=="operator"&&/\+\+|--/.test(T)){return F(K);}if(S=="operator"){return F(x);}if(S==";"){return;}if(S=="("){return F(j(")"),N(x,")"),E,K);}if(S=="."){return F(O,K);}if(S=="["){return F(j("]"),x,c("]"),E,K);}}function B(S){if(S==":"){return F(E,y);}return a(K,c(";"),E);}function O(S){if(S=="variable"){u.marked="js-property";return F();}}function o(S){if(S=="variable"){u.marked="js-property";}if(k.hasOwnProperty(S)){return F(c(":"),x);}}function N(U,S){function T(W){if(W==","){return F(U,T);}if(W==S){return F();}return F(c(S));}return function V(W){if(W==S){return F();}else{return a(U,T);}};}function n(S){if(S=="}"){return F();}return a(y,n);}function I(S,T){if(S=="variable"){m(T);return F(G);}return F();}function G(S,T){if(T=="="){return F(x,G);}if(S==","){return F(I);}}function g(S){if(S=="var"){return F(I,e);}if(S==";"){return a(e);}if(S=="variable"){return F(J);}return a(e);}function J(S,T){if(T=="in"){return F(x);}return F(K,e);}function e(S,T){if(S==";"){return F(d);}if(T=="in"){return F(x);}return F(x,c(";"),d);}function d(S){if(S!=")"){F(x);}}function i(S,T){if(S=="variable"){m(T);return F(i);}if(S=="("){return F(j(")"),s,N(q,")"),E,y,r);}}function q(S,T){if(S=="variable"){m(T);return F();}}return{startState:function(S){return{tokenize:l,reAllowed:true,cc:[],lexical:new t((S||0)-v,0,"block",false),localVars:null,context:null,indented:0};},token:function(U,T){if(U.sol()){if(!T.lexical.hasOwnProperty("align")){T.lexical.align=false;}T.indented=U.indentation();}if(U.eatSpace()){return null;}var S=T.tokenize(U,T);if(R=="comment"){return S;}T.reAllowed=R=="operator"||R=="keyword c"||R.match(/^[\[{}\(,;:]$/);return D(T,S,R,p,U);},indent:function(X,S){if(X.tokenize!=l){return 0;}var W=S&&S.charAt(0),U=X.lexical,V=U.type,T=W==V;if(V=="vardef"){return U.indented+4;}else{if(V=="form"&&W=="{"){return U.indented;}else{if(V=="stat"||V=="form"){return U.indented+v;}else{if(U.info=="switch"&&!T){return U.indented+(/^(?:case|default)\b/.test(S)?v:2*v);}else{if(U.align){return U.column+(T?0:1);}else{return U.indented+(T?0:v);}}}}}},electricChars:":{}"};});CodeMirror.defineMIME("text/javascript","javascript");CodeMirror.defineMIME("application/json",{name:"javascript",json:true});CodeMirror.defineMode("clike",function(c,e){var g=c.indentUnit,f=e.keywords,o=e.useCPP,i=e.multiLineStrings,h=e.$vars;var b=/[+\-*&%=<>!?|]/;function a(t,s,r){s.tokenize=r;return r(t,s);}var n;function l(s,r){n=s;return r;}function d(t,s){var r=t.next();if(r=='"'||r=="'"){return a(t,s,q(r));}else{if(/[\[\]{}\(\),;\:\.]/.test(r)){return l(r);}else{if(r=="#"&&o&&s.startOfLine){t.skipToEnd();return l("directive","c-like-preprocessor");}else{if(/\d/.test(r)){t.eatWhile(/[\w\.]/);return l("number","c-like-number");}else{if(r=="/"){if(t.eat("*")){return a(t,s,k);}else{if(t.eat("/")){t.skipToEnd();return l("comment","c-like-comment");}else{t.eatWhile(b);return l("operator");}}}else{if(b.test(r)){t.eatWhile(b);return l("operator");}else{if(h&&r=="$"){t.eatWhile(/[\w\$_]/);return l("word","c-like-var");}else{t.eatWhile(/[\w\$_]/);if(f&&f.propertyIsEnumerable(t.current())){return l("keyword","c-like-keyword");}return l("word","c-like-word");}}}}}}}}function q(r){return function(w,u){var v=false,t,s=false;while((t=w.next())!=null){if(t==r&&!v){s=true;break;}v=!v&&t=="\\";}if(s||!(v||i)){u.tokenize=d;}return l("string","c-like-string");};}function k(u,t){var r=false,s;while(s=u.next()){if(s=="/"&&r){t.tokenize=d;break;}r=(s=="*");}return l("comment","c-like-comment");}function p(v,s,r,u,t){this.indented=v;this.column=s;this.type=r;this.align=u;this.prev=t;}function j(t,r,s){return t.context=new p(t.indented,r,s,null,t.context);}function m(r){return r.context=r.context.prev;}return{startState:function(r){return{tokenize:d,context:new p((r||0)-g,0,"top",false),indented:0,startOfLine:true};},token:function(u,t){var r=t.context;if(u.sol()){if(r.align==null){r.align=false;}t.indented=u.indentation();t.startOfLine=true;}if(u.eatSpace()){return null;}var s=t.tokenize(u,t);if(n=="comment"){return s;}if(r.align==null){r.align=true;}if((n==";"||n==":")&&r.type=="statement"){m(t);}else{if(n=="{"){j(t,u.column(),"}");}else{if(n=="["){j(t,u.column(),"]");}else{if(n=="("){j(t,u.column(),")");}else{if(n=="}"){if(r.type=="statement"){r=m(t);}if(r.type=="}"){r=m(t);}if(r.type=="statement"){r=m(t);}}else{if(n==r.type){m(t);}else{if(r.type=="}"){j(t,u.column(),"statement");}}}}}}}t.startOfLine=false;return s;},indent:function(v,s){if(v.tokenize!=d){return 0;}var u=s&&s.charAt(0),r=v.context,t=u==r.type;if(r.type=="statement"){return r.indented+(u=="{"?0:g);}else{if(r.align){return r.column+(t?0:1);}else{return r.indented+(t?0:g);}}},electricChars:"{}"};});(function(){function b(f){var d={},e=f.split(" ");for(var c=0;c<e.length;++c){d[e[c]]=true;}return d;}var a="auto if break int case long char register continue return default short do sizeof double static else struct entry switch extern typedef float union for unsigned goto while enum void const signed volatile";CodeMirror.defineMIME("text/x-csrc",{name:"clike",useCPP:true,keywords:b(a)});CodeMirror.defineMIME("text/x-c++src",{name:"clike",useCPP:true,keywords:b(a+" asm dynamic_cast namespace reinterpret_cast try bool explicit new static_cast typeid catch false operator template typename class friend private this using const_cast inline public throw virtual delete mutable protected true wchar_t")});CodeMirror.defineMIME("text/x-java",{name:"clike",keywords:b("abstract assert boolean break byte case catch char class const continue default do double else enum extends false final finally float for goto if implements import instanceof int interface long native new null package private protected public return short static strictfp super switch synchronized this throw throws transient true try void volatile while")});}());(function(){function b(f){var d={},e=f.split(" ");for(var c=0;c<e.length;++c){d[e[c]]=true;}return d;}var a=b("abstract and array as break case catch cfunction class clone const continue declare default do else elseif enddeclare endfor endforeach endif endswitch endwhile extends final for foreach function global goto if implements interface instanceof namespace new or private protected public static switch throw try use var while xor");CodeMirror.defineMode("php",function(e,g){var i=CodeMirror.getMode(e,"text/html");var h=CodeMirror.getMode(e,"text/javascript");var f=CodeMirror.getMode(e,"text/css");var c=CodeMirror.getMode(e,{name:"clike",keywords:a,multiLineStrings:true,$vars:true});function d(l,k){if(k.curMode==i){var j=i.token(l,k.curState);if(j=="xml-processing"&&/^<\?/.test(l.current())){k.curMode=c;k.curState=k.php;k.curClose=/^\?>/;}else{if(j=="xml-tag"&&l.current()==">"&&k.curState.context){if(/^script$/i.test(k.curState.context.tagName)){k.curMode=h;k.curState=h.startState(i.indent(k.curState,""));k.curClose=/^<\/\s*script\s*>/i;}else{if(/^style$/i.test(k.curState.context.tagName)){k.curMode=f;k.curState=f.startState(i.indent(k.curState,""));k.curClose=/^<\/\s*style\s*>/i;}}}}return j;}else{if(l.match(k.curClose,false)){k.curMode=i;k.curState=k.html;k.curClose=null;return d(l,k);}else{return k.curMode.token(l,k.curState);}}}return{startState:function(){var j=i.startState();return{html:j,php:c.startState(),curMode:i,curState:j,curClose:null};},copyState:function(m){var k=m.html,l=CodeMirror.copyState(i,k),o=m.php,j=CodeMirror.copyState(c,o),n;if(m.curState==k){n=l;}else{if(m.curState==o){n=j;}else{n=CodeMirror.copyState(m.curMode,m.curState);}}return{html:l,php:j,curMode:m.curMode,curState:n,curClose:m.curClose};},token:d,indent:function(k,j){if((k.curMode!=c&&/^\s*<\//.test(j))||(k.curMode==c&&/^\?>/.test(j))){return i.indent(k.html,j);}return k.curMode.indent(k.curState,j);},electricChars:"/{}:"};});})();CodeMirror.defineMIME("application/x-httpd-php","php");CodeMirror.defineMode("htmlmixed",function(b,f){var h=CodeMirror.getMode(b,{name:"xml",htmlMode:true});var g=CodeMirror.getMode(b,"javascript");var e=CodeMirror.getMode(b,"css");function d(k,j){var i=h.token(k,j.htmlState);if(i=="xml-tag"&&k.current()==">"&&j.htmlState.context){if(/^script$/i.test(j.htmlState.context.tagName)){j.token=a;j.localState=g.startState(h.indent(j.htmlState,""));}else{if(/^style$/i.test(j.htmlState.context.tagName)){j.token=c;j.localState=e.startState(h.indent(j.htmlState,""));}}}return i;}function a(j,i){if(j.match(/^<\/\s*script\s*>/i,false)){i.token=d;i.curState=null;return d(j,i);}return g.token(j,i.localState);}function c(j,i){if(j.match(/^<\/\s*style\s*>/i,false)){i.token=d;i.localState=null;return d(j,i);}return e.token(j,i.localState);}return{startState:function(){var i=h.startState();return{token:d,localState:null,htmlState:i};},copyState:function(j){if(j.localState){var i=CodeMirror.copyState(j.token==c?e:g,j.localState);}return{token:j.token,localState:i,htmlState:CodeMirror.copyState(h,j.htmlState)};},token:function(j,i){return i.token(j,i);},indent:function(j,i){if(j.token==d||/^\s*<\//.test(i)){return h.indent(j.htmlState,i);}else{if(j.token==a){return g.indent(j.localState,i);}else{return e.indent(j.localState,i);}}},electricChars:"/{}:"};});CodeMirror.defineMIME("text/html","htmlmixed");