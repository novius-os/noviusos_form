/*!	SWFMini - a SWFObject 2.2 cut down version for webshims
 * 
 * based on SWFObject v2.2 <http://code.google.com/p/swfobject/> 
	is released under the MIT License <http://www.opensource.org/licenses/mit-license.php> 
*/
var swfmini=function(){function a(){if(!s){s=!0;for(var a=r.length,b=0;a>b;b++)r[b]()}}function b(a){s?a():r[r.length]=a}function c(){q&&d()}function d(){var a=o.getElementsByTagName("body")[0],b=e(i);b.setAttribute("type",m);var c=a.appendChild(b);if(c){var d=0;!function(){if(typeof c.GetVariable!=h){var e=c.GetVariable("$version");e&&(e=e.split(" ")[1].split(","),u.pv=[parseInt(e[0],10),parseInt(e[1],10),parseInt(e[2],10)])}else if(10>d)return d++,setTimeout(arguments.callee,10),void 0;a.removeChild(b),c=null}()}}function e(a){return o.createElement(a)}function f(a){var b=u.pv,c=a.split(".");return c[0]=parseInt(c[0],10),c[1]=parseInt(c[1],10)||0,c[2]=parseInt(c[2],10)||0,b[0]>c[0]||b[0]==c[0]&&b[1]>c[1]||b[0]==c[0]&&b[1]==c[1]&&b[2]>=c[2]?!0:!1}var g=function(){j.error("This method was removed from swfmini")},h="undefined",i="object",j=window.webshims,k="Shockwave Flash",l="ShockwaveFlash.ShockwaveFlash",m="application/x-shockwave-flash",n=window,o=document,p=navigator,q=!1,r=[c],s=!1,t=!0,u=function(){var a=typeof o.getElementById!=h&&typeof o.getElementsByTagName!=h&&typeof o.createElement!=h,b=p.userAgent.toLowerCase(),c=p.platform.toLowerCase(),d=c?/win/.test(c):/win/.test(b),e=c?/mac/.test(c):/mac/.test(b),f=/webkit/.test(b)?parseFloat(b.replace(/^.*webkit\/(\d+(\.\d+)?).*$/,"$1")):!1,g=!1,j=[0,0,0],r=null;if(typeof p.plugins!=h&&typeof p.plugins[k]==i)r=p.plugins[k].description,!r||typeof p.mimeTypes!=h&&p.mimeTypes[m]&&!p.mimeTypes[m].enabledPlugin||(q=!0,g=!1,r=r.replace(/^.*\s+(\S+\s+\S+$)/,"$1"),j[0]=parseInt(r.replace(/^(.*)\..*$/,"$1"),10),j[1]=parseInt(r.replace(/^.*\.(.*)\s.*$/,"$1"),10),j[2]=/[a-zA-Z]/.test(r)?parseInt(r.replace(/^.*[a-zA-Z]+(.*)$/,"$1"),10):0);else if(typeof n.ActiveXObject!=h)try{var s=new ActiveXObject(l);s&&(r=s.GetVariable("$version"),r&&(g=!0,r=r.split(" ")[1].split(","),j=[parseInt(r[0],10),parseInt(r[1],10),parseInt(r[2],10)]))}catch(t){}return{w3:a,pv:j,wk:f,ie:g,win:d,mac:e}}();j.ready("DOM",a),j.loader.addModule("swfmini-embed",{d:["swfmini"]});var v=f("9.0.0")?function(){return j.loader.loadList(["swfmini-embed"]),!0}:j.$.noop;return Modernizr.video?j.ready("WINDOWLOAD",v):v(),{registerObject:g,getObjectById:g,embedSWF:function(a,b,c,d,e,f,g,h,i,k){var l=arguments;v()?j.ready("swfmini-embed",function(){swfmini.embedSWF.apply(swfmini,l)}):k&&k({success:!1,id:b})},switchOffAutoHideShow:function(){t=!1},ua:u,getFlashPlayerVersion:function(){return{major:u.pv[0],minor:u.pv[1],release:u.pv[2]}},hasFlashPlayerVersion:f,createSWF:function(a,b,c){return u.w3?createSWF(a,b,c):void 0},showExpressInstall:g,removeSWF:g,createCSS:g,addDomLoadEvent:b,addLoadEvent:g,expressInstallCallback:g}}();webshims.isReady("swfmini",!0),webshims.register("form-core",function(a,b,c,d,e,f){"use strict";b.capturingEventPrevented=function(b){if(!b._isPolyfilled){var c=b.isDefaultPrevented,d=b.preventDefault;b.preventDefault=function(){return clearTimeout(a.data(b.target,b.type+"DefaultPrevented")),a.data(b.target,b.type+"DefaultPrevented",setTimeout(function(){a.removeData(b.target,b.type+"DefaultPrevented")},30)),d.apply(this,arguments)},b.isDefaultPrevented=function(){return!(!c.apply(this,arguments)&&!a.data(b.target,b.type+"DefaultPrevented"))},b._isPolyfilled=!0}},Modernizr.formvalidation&&!b.bugs.bustedValidity&&b.capturingEvents(["invalid"],!0);var g=b.modules,h=function(b){return(a.prop(b,"validity")||{valid:1}).valid},i=function(){var c=["form-validation"];f.lazyCustomMessages&&(f.customMessages=!0,c.push("form-message")),b._getAutoEnhance(f.customDatalist)&&(f.fD=!0,c.push("form-datalist")),f.addValidators&&c.push("form-validators"),b.reTest(c),a(d).off(".lazyloadvalidation")},j=/^(?:form|fieldset)$/i,k=function(b){var c=!1;return a(b).jProp("elements").each(function(){return!j.test(this.nodeName||"")&&(c=a(this).is(":invalid"))?!1:void 0}),c},l=function(){var c,e,f=a.expr[":"];if(a.extend(f,{"valid-element":function(b){return j.test(b.nodeName||"")?!k(b):!(!a.prop(b,"willValidate")||!h(b))},"invalid-element":function(b){return j.test(b.nodeName||"")?k(b):!(!a.prop(b,"willValidate")||h(b))},"required-element":function(b){return!(!a.prop(b,"willValidate")||!a.prop(b,"required"))},"user-error":function(b){return a.prop(b,"willValidate")&&a(b).hasClass("user-error")},"optional-element":function(b){return!(!a.prop(b,"willValidate")||a.prop(b,"required")!==!1)}}),["valid","invalid","required","optional"].forEach(function(b){f[b]=a.expr[":"][b+"-element"]}),Modernizr.fieldsetdisabled&&!a('<fieldset disabled=""><input /><input /></fieldset>').find(":disabled").filter(":disabled").is(":disabled")&&(c=a.find.matches,e={":disabled":1,":enabled":1},a.find.matches=function(a,b){return e[a]?c.call(this,"*"+a,b):c.apply(this,arguments)},a.extend(f,{enabled:function(b){return b.disabled===!1&&!a(b).is("fieldset[disabled] *")},disabled:function(b){return b.disabled===!0||"disabled"in b&&a(b).is("fieldset[disabled] *")}})),"unknown"==typeof d.activeElement){var g=f.focus;f.focus=function(){try{return g.apply(this,arguments)}catch(a){b.error(a)}return!1}}};a.expr.filters?l():b.ready("sizzle",l),b.triggerInlineForm=function(b,c){a(b).trigger(c)};var m=function(a,c,d){i(),b.ready("form-validation",function(){a[c].apply(a,d)})},n="transitionDelay"in d.documentElement.style?"":" no-transition",o=b.cfg.wspopover;o.position||o.position===!1||(o.position={at:"left bottom",my:"left top",collision:"fit flip"}),b.wsPopover={id:0,_create:function(){this.options=a.extend(!0,{},o,this.options),this.id=b.wsPopover.id++,this.eventns=".wsoverlay"+this.id,this.timers={},this.element=a('<div class="ws-popover'+n+'" tabindex="-1"><div class="ws-po-outerbox"><div class="ws-po-arrow"><div class="ws-po-arrowbox" /></div><div class="ws-po-box" /></div></div>'),this.contentElement=a(".ws-po-box",this.element),this.lastElement=a([]),this.bindElement(),this.element.data("wspopover",this)},options:{},content:function(a){this.contentElement.html(a)},bindElement:function(){var a=this,b=function(){a.stopBlur=!1};this.preventBlur=function(){a.stopBlur=!0,clearTimeout(a.timers.stopBlur),a.timers.stopBlur=setTimeout(b,9)},this.element.on({mousedown:this.preventBlur})},show:function(){m(this,"show",arguments)}},b.validityAlert={showFor:function(){m(this,"showFor",arguments)}},b.getContentValidationMessage=function(c,d,e){b.errorbox&&b.errorbox.initIvalContentMessage&&b.errorbox.initIvalContentMessage(c);var f=(b.getOptions&&b.errorbox?b.getOptions(c,"errormessage",!1,!0):a(c).data("errormessage"))||c.getAttribute("x-moz-errormessage")||"";return e&&f[e]?f=f[e]:f&&(d=d||a.prop(c,"validity")||{valid:1},d.valid&&(f="")),"object"==typeof f&&(d=d||a.prop(c,"validity")||{valid:1},d.valid||(a.each(d,function(a,b){return b&&"valid"!=a&&f[a]?(f=f[a],!1):void 0}),"object"==typeof f&&(d.typeMismatch&&f.badInput&&(f=f.badInput),d.badInput&&f.typeMismatch&&(f=f.typeMismatch)))),"object"==typeof f&&(f=f.defaultMessage),b.replaceValidationplaceholder&&(f=b.replaceValidationplaceholder(c,f)),f||""},a.fn.getErrorMessage=function(c){var d="",e=this[0];return e&&(d=b.getContentValidationMessage(e,!1,c)||a.prop(e,"customValidationMessage")||a.prop(e,"validationMessage")),d},a.event.special.valuevalidation={setup:function(){var b=a(this).data()||a.data(this,{});"valuevalidation"in b||(b.valuevalidation=!0)}},a(d).on("focusin.lazyloadvalidation",function(a){"form"in a.target&&i()}),b.ready("WINDOWLOAD",i),g["form-number-date-ui"].loaded&&g["form-number-date-api"].test()&&b.isReady("form-number-date-ui",!0)}),function(a,b){"use strict";var c,d,e=a.audio&&a.video,f=!1,g=b.bugs,h="mediaelement-jaris",i=function(){b.ready(h,function(){b.mediaelement.createSWF||(b.mediaelement.loadSwf=!0,b.reTest([h],e))})},j=b.cfg,k=j.mediaelement;if(!k)return b.error("mediaelement wasn't implemented but loaded"),void 0;if(e){var l=document.createElement("video");a.videoBuffered="buffered"in l,a.mediaDefaultMuted="defaultMuted"in l,f="loop"in l,a.mediaLoop=f,b.capturingEvents(["play","playing","waiting","paused","ended","durationchange","loadedmetadata","canplay","volumechange"]),(!a.videoBuffered||!f||!a.mediaDefaultMuted&&-1!=navigator.userAgent.indexOf("MSIE")&&"ActiveXObject"in window)&&(b.addPolyfill("mediaelement-native-fix",{d:["dom-support"]}),b.loader.loadList(["mediaelement-native-fix"]))}a.track&&!g.track&&!function(){if(!g.track){window.VTTCue&&!window.TextTrackCue?window.TextTrackCue=window.VTTCue:window.VTTCue||(window.VTTCue=window.TextTrackCue);try{new VTTCue(2,3,"")}catch(a){g.track=!0}}}(),c=a.track&&!g.track,b.register("mediaelement-core",function(b,g,j,k,l,m){d=swfmini.hasFlashPlayerVersion("10.0.3"),b("html").addClass(d?"swf":"no-swf");var n=g.mediaelement;n.parseRtmp=function(a){var b,c,d,e=a.src.split("://"),f=e[1].split("/");for(a.server=e[0]+"://"+f[0]+"/",a.streamId=[],b=1,c=f.length;c>b;b++)d||-1===f[b].indexOf(":")||(f[b]=f[b].split(":")[1],d=!0),d?a.streamId.push(f[b]):a.server+=f[b]+"/";a.streamId.length||g.error("Could not parse rtmp url"),a.streamId=a.streamId.join("/")};var o=function(a,c){a=b(a);var d,e={src:a.attr("src")||"",elem:a,srcProp:a.prop("src")};return e.src?(d=a.attr("data-server"),null!=d&&(e.server=d),d=a.attr("type")||a.attr("data-type"),d?(e.type=d,e.container=b.trim(d.split(";")[0])):(c||(c=a[0].nodeName.toLowerCase(),"source"==c&&(c=(a.closest("video, audio")[0]||{nodeName:"video"}).nodeName.toLowerCase())),e.server?(e.type=c+"/rtmp",e.container=c+"/rtmp"):(d=n.getTypeForSrc(e.src,c,e),d&&(e.type=d,e.container=d))),e.container||b(a).attr("data-wsrecheckmimetype",""),d=a.attr("media"),d&&(e.media=d),("audio/rtmp"==e.type||"video/rtmp"==e.type)&&(e.server?e.streamId=e.src:n.parseRtmp(e)),e):e},p=!d&&"postMessage"in j&&e,q=function(){q.loaded||(q.loaded=!0,m.noAutoTrack||g.ready("WINDOWLOAD",function(){s(),g.loader.loadList(["track-ui"])}))},r=function(){var a;return function(){!a&&p&&(a=!0,g.loader.loadScript("https://www.youtube.com/player_api"),b(function(){g._polyfill(["mediaelement-yt"])}))}}(),s=function(){d?i():r()};g.addPolyfill("mediaelement-yt",{test:!p,d:["dom-support"]}),n.mimeTypes={audio:{"audio/ogg":["ogg","oga","ogm"],'audio/ogg;codecs="opus"':"opus","audio/mpeg":["mp2","mp3","mpga","mpega"],"audio/mp4":["mp4","mpg4","m4r","m4a","m4p","m4b","aac"],"audio/wav":["wav"],"audio/3gpp":["3gp","3gpp"],"audio/webm":["webm"],"audio/fla":["flv","f4a","fla"],"application/x-mpegURL":["m3u8","m3u"]},video:{"video/ogg":["ogg","ogv","ogm"],"video/mpeg":["mpg","mpeg","mpe"],"video/mp4":["mp4","mpg4","m4v"],"video/quicktime":["mov","qt"],"video/x-msvideo":["avi"],"video/x-ms-asf":["asf","asx"],"video/flv":["flv","f4v"],"video/3gpp":["3gp","3gpp"],"video/webm":["webm"],"application/x-mpegURL":["m3u8","m3u"],"video/MP2T":["ts"]}},n.mimeTypes.source=b.extend({},n.mimeTypes.audio,n.mimeTypes.video),n.getTypeForSrc=function(a,c){if(-1!=a.indexOf("youtube.com/watch?")||-1!=a.indexOf("youtube.com/v/"))return"video/youtube";if(0===a.indexOf("rtmp"))return c+"/rtmp";a=a.split("?")[0].split("#")[0].split("."),a=a[a.length-1];var d;return b.each(n.mimeTypes[c],function(b,c){return-1!==c.indexOf(a)?(d=b,!1):void 0}),d},n.srces=function(a,c){if(a=b(a),!c){c=[];var d=a[0].nodeName.toLowerCase(),e=o(a,d);return e.src?c.push(e):b("source",a).each(function(){e=o(this,d),e.src&&c.push(e)}),c}g.error("setting sources was removed.")},b.fn.loadMediaSrc=function(){g.error("loadMediaSrc was removed.")},n.swfMimeTypes=["video/3gpp","video/x-msvideo","video/quicktime","video/x-m4v","video/mp4","video/m4p","video/x-flv","video/flv","audio/mpeg","audio/aac","audio/mp4","audio/x-m4a","audio/m4a","audio/mp3","audio/x-fla","audio/fla","youtube/flv","video/jarisplayer","jarisplayer/jarisplayer","video/youtube","video/rtmp","audio/rtmp"],n.canThirdPlaySrces=function(a,c){var e="";return(d||p)&&(a=b(a),c=c||n.srces(a),b.each(c,function(a,b){return b.container&&b.src&&(d&&-1!=n.swfMimeTypes.indexOf(b.container)||p&&"video/youtube"==b.container)?(e=b,!1):void 0})),e};var t={};n.canNativePlaySrces=function(a,c){var d="";if(e){a=b(a);var f=(a[0].nodeName||"").toLowerCase(),g=(t[f]||{prop:{_supvalue:!1}}).prop._supvalue||a[0].canPlayType;if(!g)return d;c=c||n.srces(a),b.each(c,function(b,c){return c.type&&g.call(a[0],c.type)?(d=c,!1):void 0})}return d};var u=/^\s*application\/octet\-stream\s*$/i,v=function(){var a=u.test(b.attr(this,"type")||"");return a&&b(this).removeAttr("type"),a};n.setError=function(a,c){if(b("source",a).filter(v).length){g.error('"application/octet-stream" is a useless mimetype for audio/video. Please change this attribute.');try{b(a).mediaLoad()}catch(d){}}else c||(c="can't play sources"),b(a).pause().data("mediaerror",c),g.error("mediaelementError: "+c),setTimeout(function(){b(a).data("mediaerror")&&b(a).addClass("media-error").trigger("mediaerror")},1)};var w=function(){var a,c=d?h:"mediaelement-yt";return function(d,e,f){g.ready(c,function(){n.createSWF&&b(d).parent()[0]?n.createSWF(d,e,f):a||(a=!0,s(),w(d,e,f))}),a||!p||n.createSWF||r()}}(),x=function(a,b,c,d,e){var f;c||c!==!1&&b&&"third"==b.isActive?(f=n.canThirdPlaySrces(a,d),f?w(a,f,b):e?n.setError(a,!1):x(a,b,!1,d,!0)):(f=n.canNativePlaySrces(a,d),f?b&&"third"==b.isActive&&n.setActive(a,"html5",b):e?(n.setError(a,!1),b&&"third"==b.isActive&&n.setActive(a,"html5",b)):x(a,b,!0,d,!0))},y=/^(?:embed|object|datalist)$/i,z=function(a,c){var d=g.data(a,"mediaelementBase")||g.data(a,"mediaelementBase",{}),e=n.srces(a),f=a.parentNode;clearTimeout(d.loadTimer),b(a).removeClass("media-error"),b.data(a,"mediaerror",!1),e.length&&f&&1==f.nodeType&&!y.test(f.nodeName||"")&&(c=c||g.data(a,"mediaelement"),n.sortMedia&&e.sort(n.sortMedia),x(a,c,m.preferFlash||l,e))};n.selectSource=z,b(k).on("ended",function(a){var c=g.data(a.target,"mediaelement");(!f||c&&"html5"!=c.isActive||b.prop(a.target,"loop"))&&setTimeout(function(){!b.prop(a.target,"paused")&&b.prop(a.target,"loop")&&b(a.target).prop("currentTime",0).play()},1)});var A=!1,B=function(){var c=function(){g.implement(this,"mediaelement")&&(z(this),a.mediaDefaultMuted||null==b.attr(this,"muted")||b.prop(this,"muted",!0))};g.ready("dom-support",function(){A=!0,f||g.defineNodeNamesBooleanProperty(["audio","video"],"loop"),["audio","video"].forEach(function(a){var c;c=g.defineNodeNameProperty(a,"load",{prop:{value:function(){var a=g.data(this,"mediaelement");z(this,a),!e||a&&"html5"!=a.isActive||!c.prop._supvalue||c.prop._supvalue.apply(this,arguments),b(this).triggerHandler("wsmediareload")}}}),t[a]=g.defineNodeNameProperty(a,"canPlayType",{prop:{value:function(c){var f="";return e&&t[a].prop._supvalue&&(f=t[a].prop._supvalue.call(this,c),"no"==f&&(f="")),!f&&d&&(c=b.trim((c||"").split(";")[0]),-1!=n.swfMimeTypes.indexOf(c)&&(f="maybe")),f}}})}),g.onNodeNamesPropertyModify(["audio","video"],["src","poster"],{set:function(){var a=this,b=g.data(a,"mediaelementBase")||g.data(a,"mediaelementBase",{});clearTimeout(b.loadTimer),b.loadTimer=setTimeout(function(){z(a),a=null},9)}}),g.addReady(function(a,d){var e=b("video, audio",a).add(d.filter("video, audio")).each(c);!q.loaded&&b("track",e).length&&q(),e=null})}),e&&!A&&g.addReady(function(a,c){A||b("video, audio",a).add(c.filter("video, audio")).each(function(){return n.canNativePlaySrces(this)?void 0:(s(),A=!0,!1)})})};c&&g.defineProperty(TextTrack.prototype,"shimActiveCues",{get:function(){return this._shimActiveCues||this.activeCues}}),e?(g.isReady("mediaelement-core",!0),B(),g.ready("WINDOWLOAD mediaelement",s)):g.ready(h,B),g.ready("track",q)})}(Modernizr,webshims);