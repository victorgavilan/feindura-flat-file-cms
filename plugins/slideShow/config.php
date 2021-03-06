<?php
/**
 * @package [Plugins]
 * @subpackage slideShow
 *
 * If the key contains certain words, it will create different inputs. The check for this keywords in case insensitive (means "path" and "Path" is the same).
 *
 * key contains (without the ...):<br>
 * - "...Url"                        The value of this setting will be checked by {@link XssFilter::url()}<br>
 * - "...Path"                       The value of this setting will be checked by {@link XssFilter::path()}<br>
 * - "...Number"                     The value of this setting will be checked by {@link XssFilter::number()}<br>
 * - "...Text" or nothing            The value of this setting will be checked by {@link XssFilter::text()}<br>
 * - "...Selection"                  Will create a <select>. value is also the name and should be an array like: array(0 => 'value1', 1 => 'value2')
 * - "...JsFunction"                 Creates a button, which will call a javascript function with this value as name, like <a href="#" onclick="exampleFunction(); return false;">
 * - "...Hidden"                     It will create a hidden text input field, with the setting value as input value
 * - "...Script"                     It will create a <script> tag with the value as content, before the plugin settings <table> tag.
 * - "...Print"                      It will just display this string after the last plugin setting ..</td></tr> and before the next <tr><td>... This could be used to create custom config settings in the plugin settings table.
 * - if the value is a boolean       It will create a checkbox and will check this value against {@link XssFilter::bool()}<br>
 *
 *
 * Example
 * <samp>
 * $pluginConfig['linkPath'] = ''; // would use the path filter
 * </samp>
 *
 * @see XssFilter::url()
 * @see XssFilter::path()
 * @see XssFilter::number()
 * @see XssFilter::bool()
 * @see XssFilter::text()
 */

$pluginConfig['selectImagesJsFunction'] = 'slideShowSelectImage()';
$pluginConfig['imagesHidden']           = '';
$pluginConfig['widthNumber']            = 600;
$pluginConfig['heightNumber']           = 350;
$pluginConfig['intervalNumber']         = 3;
// effects
$pluginConfig['effectSelection'][]      = 'fade';
$pluginConfig['effectSelection'][]      = 'fold';
$pluginConfig['effectSelection'][]      = 'random';
// horizontal effects
$pluginConfig['effectSelection'][]      = 'wipeDown';
$pluginConfig['effectSelection'][]      = 'wipeUp';
$pluginConfig['effectSelection'][]      = 'sliceLeftDown';
$pluginConfig['effectSelection'][]      = 'sliceLeftUp';
$pluginConfig['effectSelection'][]      = 'sliceLeftRightDown';
$pluginConfig['effectSelection'][]      = 'sliceLeftRightUp';
$pluginConfig['effectSelection'][]      = 'sliceRightDown';
$pluginConfig['effectSelection'][]      = 'sliceRightUp';
// vertical effects
$pluginConfig['effectSelection'][]      = 'wipeRight';
$pluginConfig['effectSelection'][]      = 'wipeLeft';
$pluginConfig['effectSelection'][]      = 'sliceDownLeft';
$pluginConfig['effectSelection'][]      = 'sliceDownRight';
$pluginConfig['effectSelection'][]      = 'sliceUpDownLeft';
$pluginConfig['effectSelection'][]      = 'sliceUpDownRight';
$pluginConfig['effectSelection'][]      = 'sliceUpLeft';
$pluginConfig['effectSelection'][]      = 'sliceUpRight';
// scripts
$pluginConfig['selectImagesScript']     = '

// vars
var fileManagerSlideShow;

// -> open filemanager when link get clicked
slideShowSelectImage = function() {
  fileManagerSlideShow.show();
}

// loads the gallery javascript
Asset.javascript("library/thirdparty/MooTools-FileManager/Source/Gallery.js",{
    onLoad: function(){

        var transformBr = function(object) {
            // change line breaks into <br>
            Object.each(object, function(text,key){
                text = text.replace(/\<br\>/g,"\n");
                object[key] = text;
            });
            return object;
        };

        // vars
        var filemanagerDimmer = new Element("div",{styles:{"z-index":20008,"position":"fixed", top: 0, "width": "100%","height": "100%"}});

        // get the current gallery
        var galleryItems = JSON.decode(document.id("feinduraPlugin_slideShow_config_imagesHidden").get("value"));
        galleryItems = transformBr(galleryItems);
        var galleryDir = (typeOf(galleryItems) == "object") ? Object.keys(galleryItems)[0] : "";


        // ->> include filemanager gallery
        var hideFileManager = function(){this.hide();}
        fileManagerSlideShow = new FileManager.Gallery({
            directory: galleryDir,
            url: "library/controllers/filemanager.controller.php",
            assetBasePath: "library/thirdparty/MooTools-FileManager/Assets",
            documentRootPath: "'.DOCUMENTROOT.'",
            language: "'.$_SESSION["feinduraSession"]["backendLanguage"].'",
            propagateData: {"'.session_name().'":"'.session_id().'"},
            filter: "image",
            deliverPathAsLegalURL: true,
            destroy: true,
            upload: true,
            move_or_copy: true,
            rename: true,
            createFolders: true,
            download: true,
            hideOnClick: true,
            hideOverlay: true,
            hideOnDelete: false,
            listPaginationSize: 100,
            zIndex: 20020,
            onShow: function(mgr) {
                galleryItems = JSON.decode(document.id("feinduraPlugin_slideShow_config_imagesHidden").get("value"));
                galleryItems = transformBr(galleryItems);
                mgr.populate(galleryItems, false);

                window.location.hash = "#none";

                filemanagerDimmer.inject(document.body);
                filemanagerDimmer.addEvent("click",hideFileManager.bind(this));
              },
            onHide: function() {
                filemanagerDimmer.removeEvent("click",hideFileManager);
                filemanagerDimmer.dispose();
              },
            onComplete: function(serialized, files, legal_root_dir, mgr) {
                // change line breaks into <br>
                Object.each(serialized, function(text,key,object){
                    text = text.replace(/[\n\r]/g,"<br>");
                    serialized[key] = text;
                });
                document.id("feinduraPlugin_slideShow_config_imagesHidden").set("value", decodeURIComponent(JSON.encode(serialized)));
              }
        });
        fileManagerSlideShow.filemanager.setStyle("width","75%");
        fileManagerSlideShow.filemanager.setStyle("height","70%");
    }
});
';

return $pluginConfig;
?>