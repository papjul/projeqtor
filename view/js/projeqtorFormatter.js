/*** COPYRIGHT NOTICE *********************************************************
 *
 * Copyright 2009-2016 ProjeQtOr - Pascal BERNARD - support@projeqtor.org
 * Contributors : -
 *
 * This file is part of ProjeQtOr.
 * 
 * ProjeQtOr is free software: you can redistribute it and/or modify it under 
 * the terms of the GNU Affero General Public License as published by the Free 
 * Software Foundation, either version 3 of the License, or (at your option) 
 * any later version.
 * 
 * ProjeQtOr is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS 
 * FOR A PARTICULAR PURPOSE.  See the GNU Affero General Public License for 
 * more details.
 *
 * You should have received a copy of the GNU Affero General Public License along with
 * ProjeQtOr. If not, see <http://www.gnu.org/licenses/>.
 *
 * You can get complete code of ProjeQtOr, other resource, help and information
 * about contributors at http://www.projeqtor.org 
 *     
 *** DO NOT REMOVE THIS NOTICE ************************************************/

// ============================================================================
// All specific ProjeQtOr functions and variables
// This file is included in the main.php page, to be reachable in every context
// ============================================================================
//=============================================================================
//= global formating functions)
//=============================================================================
/**
 * Format a JS date as YYYY-MM-DD
 * 
 * @param value
 *          the value
 * @return the formatted value
 */
function formatDate(date) {
  if (!date) {
    return '';
  }
  var month = date.getMonth() + 1;
  var year = date.getFullYear();
  var day = date.getDate();
  month = (month < 10) ? "0" + month : month;
  day = (day < 10) ? "0" + day : day;
  return year + "-" + month + "-" + day;
}
function formatTime(date) {
  if (!date) {
    return '';
  }
  var hours = date.getHours();
  var minutes = date.getMinutes();
  var seconds = date.getSeconds();
  hours = (hours < 10) ? "0" + hours : hours;
  minutes = (minutes < 10) ? "0" + minutes : minutes;
  seconds = (seconds < 10) ? "0" + seconds : seconds;
  return hours + ":" + minutes + ":" + seconds;
}

function getDate(dateString) {
  if (dateString.length != 10)
    return null;
  return new Date(dateString.substring(0, 4), parseInt(dateString.substring(5,
      7), 10) - 1, dateString.substring(8));
}

// ============================================================================
// = FORMATTERS (available for dojox.DataGrid formating)
// ============================================================================

/**
 * ============================================================================
 * Format boolean to present a chechbox (checked or not depending on the value)
 * 
 * @param value
 *          the value of the boolean (true or false)
 * @return the formatted value as an image (html code)
 */
function booleanFormatter(value) {
  if (value != 0) {
    return '<div style="width:100%;text-align:center;"><img src="img/checkedOK.png" width="12" height="12" /></div>';
  } else {
    return '<div style="width:100%;text-align:center;"><img src="img/checkedKO.png" width="12" height="12" /></div>';
  }
}

/**
 * ============================================================================
 * Format value to present a color
 * 
 * @param value
 *          the value of the boolean (true or false)
 * @return the formatted value as an image (html code)
 */
function colorFormatter(value) {
  if (value) {
    return '<table width="100%"><tr><td style="min-height:20px;border-radius: 10px; padding: 5px 5px !important;background-color: '
        + value + '; width: 100%;">&nbsp;</td></tr></table>';
  } else {
    return '';
  }
}

/**
 * ============================================================================
 * Format value to present a name in a colored field
 * 
 * @param value
 *          the value of the boolean (true or false)
 * @return the formatted value as an image (html code)
 */
function colorNameFormatter(value) {
  if (value) {
    var tab = value.split("#split#");
    if (tab.length > 1) {
      if (tab.length == 2) { // just found : val #split# color
        var val = tab[0];
        var color = tab[1];
        var order = '';
      } else if (tab.length == 3) { // val #split# color #split# order
        var val = tab[1];
        var color = tab[2];
        var order = tab[0];
      } else { // should not be found
        return value;
      }
      var foreColor = '#000000';
      if (color.length == 7) {
        var red = color.substr(1, 2);
        var green = color.substr(3, 2);
        var blue = color.substr(5, 2);
        var light = (0.3) * parseInt(red, 16) + (0.6) * parseInt(green, 16)
            + (0.1) * parseInt(blue, 16);
        if (light < 128) {
          foreColor = '#FFFFFF';
        }
      }
      return '<span style="display:none;">'
          + order
          + '</span><table width="100%" style="min-height:20px !important;"><tr style="height:100% !important;"><td style="text-align: center;border-radius: 10px; padding: 5px 5px !important;background-color: '
          + color + '; color:' + foreColor + ';width: 100%;">' + val
          + '</td></tr></table>';
    } else {
      return value;
    }
  } else {
    return '';
  }
}
function colorTranslateNameFormatter(value) {
  if (value) {
    var tab = value.split("#split#");
    if (tab.length > 1) {
      if (tab.length == 2) { // just found : val #split# color
        var val = tab[0];
        var color = tab[1];
        var order = '';
      } else if (tab.length == 3) { // val #split# color #split# order
        var val = tab[1];
        var color = tab[2];
        var order = tab[0];
      } else { // should not be found
        return value;
      }
      var foreColor = '#000000';
      if (color.length == 7) {
        var red = color.substr(1, 2);
        var green = color.substr(3, 2);
        var blue = color.substr(5, 2);
        var light = (0.3) * parseInt(red, 16) + (0.6) * parseInt(green, 16)
            + (0.1) * parseInt(blue, 16);
        if (light < 128) {
          foreColor = '#FFFFFF';
        }
      }
      return '<span style="display:none;">' + order
          + '</span><table width="100%"><tr><td style="background-color: '
          + color + '; color:' + foreColor + ';width: 100%;">' + i18n(val)
          + '</td></tr></table>';
    } else {
      return i18n(value);
    }
  } else {
    return '';
  }
}

/**
 * ============================================================================
 * Format boolean to present a color
 * 
 * @param value
 *          the value of the boolean (true or false)
 * @return the formatted value as an image (html code)
 */
function translateFormatter(value, prefix) {
  if (value) {
    return i18n(value);
  } else {
    return '';
  }
}

/**
 * ============================================================================
 * Format percent value
 * 
 * @param value
 *          the value of the boolean (true or false)
 * @return the formatted value as an image (html code)
 */
function percentFormatter(value) {
  if (value) {
    var pct = parseInt(value, 10);
    var pctTxt = '<div style="width:100%;text-align:center;">' + pct
        + '&nbsp;%</div>';
    pctTxt += '<div style="height:3px;width:100%;position: relative; bottom:0px;">';
    pctTxt += '<div style="height:3px;width:' + pct
        + '%;position: absolute;left:0%;background-color:#AAFFAA">&nbsp;</div>';
    pctTxt += '<div style="height:3px;width:' + (100 - pct)
        + '%;position: absolute;left:' + pct
        + '%; background-color:#FFAAAA">&nbsp;</div>';
    pctTxt += '</div>';
    return pctTxt;
  } else {
    return '';
  }
}

function percentSimpleFormatter(value) {
  if (value) {
    return value+"&nbsp;%";
  } else {
    return '';
  }
}

function dayFormatter(value) {
  var val = parseInt(value, 10);
  if (value=='') {
    return '';
  } else if (val<=1) {
    return value+' '+i18n('day');
  } else {
    return value+' '+i18n('days');;
  }
}
/**
 * ============================================================================
 * Format numeric value (removes leading zeros)
 * 
 * @param value
 *          the value
 * @return the formatted value
 */
function numericFormatter(value) {
  // result=dojo.number.format(value);
  var result = value.replace(/^0+/g, '');
  // result = value.replace(/^0+/g,'');
  return result;
}

function durationFormatter(value) {
  return value+' '+i18n('shortDay');
}

var hiddenField='<span style="color:#AAAAAA">(...)</span>';
function workFormatter(value) {
  if (value == null) return null;
  if (value == '-') return hiddenField;
  // result=dojo.number.format(value);
  var paramUnit=top.paramWorkUnit;
  if (dojo.byId('objectClass') && (dojo.byId('objectClass').value=='Ticket' || dojo.byId('objectClass').value=='TicketSimple')) {
    paramUnit=top.paramImputationUnit;
  } 
  if (paramUnit != 'days') {
       value = value * top.paramHoursPerDay;
    }
  roundedValue = dojo.number.format(Math.round(value * 100) / 100);
  // var result = roundedValue.replace(/^0+/g,'');
  // result = value.replace(/^0+/g,'');
  var unit = (paramUnit == 'days') ? i18n('shortDay')
      : i18n('shortHour');
  return roundedValue + '&nbsp;' + unit;
}

function costFormatter(value) {
  if (value == null) return null;
  if (value == '-') return hiddenField;
  // result=dojo.number.format(value);
  roundedValue = dojo.number.format(Math.round(value * 100) / 100);
  // var result = roundedValue.replace(/^0+/g,'');
  // result = value.replace(/^0+/g,'');
  if (top.paramCurrencyPosition == 'before') {
    return top.paramCurrency + '&nbsp;' + roundedValue;
  } else {
    return roundedValue + '&nbsp;' + top.paramCurrency;
  }
}
/**
 * ============================================================================
 * Format date value (depends on locale)
 * 
 * @param value
 *          the value
 * @return the formatted value
 */
function dateFormatter(value) {
  fmt = top.getBrowserLocaleDateFormatJs();
  if (value.length == 10) {
    vDate = dojo.date.locale.parse(value, {
      selector : "date",
      datePattern : "yyyy-MM-dd"
    });
    return dojo.date.locale.format(vDate, {
      datePattern : fmt,
      formatLength : "short",
      fullYear : true,
      selector : "date"
    });
  } else {
    return value;
  }
}

function longDateFormatter(value) {
  if (value.length == 10) {
    vDate = dojo.date.locale.parse(value, {
      selector : "date",
      datePattern : "yyyy-MM-dd"
    });
    return dojo.date.locale.format(vDate, {
      formatLength : "long",
      fullYear : true,
      selector : "date"
    });
  } else {
    return value;
  }
}
/**
 * ============================================================================
 * Format date & time value (depends on locale)
 * 
 * @param value
 *          the value
 * @return the formatted value
 */
/**
 * ============================================================================
 * Format date & time value (depends on locale)
 * 
 * @param value
 *          the value
 * @return the formatted value
 */
function dateTimeFormatter(value) {
  fmt = top.getBrowserLocaleDateFormatJs();
  if (value && value.length == 19) {
    vDate = dojo.date.locale.parse(value, {
      datePattern : "yyyy-MM-dd",
      timePattern : "HH:mm:ss",
      selector : 'date and time'
    });
    if (!vDate) {
      vDate = new Date(value.substr(0, 4),
          (parseInt(value.substr(5, 2), 10)) - 1, value.substr(8, 2), value
              .substr(11, 2), value.substr(14, 2), value.substr(17, 2), 0);
      if (!vDate) {
        return dateFormatter(value.substr(0, 10)) + ":" + value.substr(11, 5);
      }
    }
    return dojo.date.locale.format(vDate, {
      datePattern : fmt,
      formatLength : "short",
      fullYear : true
    });
  } else {
    return value;
  }
}
function timeFormatter(value) {
  if (value.length == 19) {
    vDate = dojo.date.locale.parse(value, {
      datePattern : "yyyy-MM-dd",
      timePattern : "HH:mm:ss",
      selector : 'date and time'
    });
    if (!vDate) {
      vDate = new Date(value.substr(0, 4),
          (parseInt(value.substr(5, 2), 10)) - 1, value.substr(8, 2), value
              .substr(11, 2), value.substr(14, 2), value.substr(17, 2), 0);
      if (!vDate) {
        return value.substr(11, 5);
      }
    }
    return dojo.date.locale.format(vDate, {
      formatLength : "time"
    });
  } else {
    return value;
  }
}

function sortableFormatter(value) {
  if (!value) {
    return '';
  }
  var tab = value.split('.');
  var result = '';
  for (i = 0; i < tab.length; i++) {
    result += (result != "") ? "." : "";
    result += tab[i].replace(/^0+/, "");
  }
  return result;
}

function thumb16(value) {
  return thumb(value, 16);
}
function thumb22(value) {
  return thumb(value, 22);
}
function thumb32(value) {
  return thumb(value, 32);
}
function thumb48(value) {
  return thumb(value, 48);
}
function thumb64(value) {
  return thumb(value, 64);
}
function thumbName16(value) {
  return thumb(value, 16);
}
function thumbName22(value) {
  return thumb(value, 22);
}
function thumbName32(value) {
  return thumb(value, 32);
}
function thumbName48(value) {
  return thumb(value, 48);
}
function thumbName64(value) {
  return thumb(value, 64);
}
function thumb(value, size) {
  if (value == "##" || value == "####")
    return "";
  if (!size)
    size = 32;
  var tab = value.split('#');
  filePath = tab[0];
  var nocache='';
  var searchNocache=filePath.indexOf('?');
  if (searchNocache>0) {
    nocache=filePath.substring(searchNocache);
  }
  thumbObjectClass = 'Attachment';
  if (tab.length > 3) {
    thumbObjectClass = tab[3];
  }
  thumbName="";
  if (tab.length > 4) {
    thumbName = tab[4];
  }
  thumbObjectId = tab[1];
  fileName = tab[2];
  var radius=Math.round(size/2);
  var result = '';
  if (filePath) {
    result+= '<div style="'+((thumbName)?'text-align:left;':'text-align:center;')+'">';    
    result+='<img style="border-radius:'+radius+'px;height:' + size + 'px;'+((thumbName)?'float:left;':'')+'" src="' + filePath + '"';
    if (filePath.substr(0,23) != '../view/img/Affectable/') {
      result+=' onMouseOver="showBigImage(\''+thumbObjectClass+'\',\''+thumbObjectId+'\',this,null,null,\''+nocache+'\');"';
      result+=' onMouseOut="hideBigImage();"';
    }
    result+='/>';
    if (thumbName) {
      // text-shadow:1px 1px #FFFFFF; Can ease view when test is over thumb, but is ugly when line is selected (when text color is white)
      result+='<div style="margin-left:'+(size+2)+'px;">'+thumbName+'</div>';
    }
    result+='</div>';
  } else {
    result=thumbName;
  }
  return result;
}

function iconFormatter(value) {
  if (!value)
    return "";
  return '<img style="height:22px" src="icons/' + value + '" />';
}

function privateFormatter(value) {
  if (value==0) { 
    return "";
  } else { 
    return '<div style="width:100%;text-align:center"><img style="height:16px" src="img/private.png" /></div>';
  }
}

var cryptFrom = "A;B;C;D;E;F;G;H;I;J;K;L.M;N;O;P;Q;R;S;T;U;V;W;X;Y;Z;a;à;â;b;c;ç;d;e;é;è;ê;f;g;h;i;î;ï;j;k;l;m;n;o;ô;p;q;r;s;t;u;û;ù;v;w;x;y;z;; ;?;';(;)1;2;3;4;5;6;7;8;9;0"
    .split(';');
var cryptTo = "2;3;4;5;6;7;8;9;0;A;B;C;D;E;F;G;H;I;J;K;L.M;N;O;P;Q;R;S;T;U;V;W;X;Y;Z;a;à;â;b;c;ç;d;e;é;è;ê;f;g;h;i;î;ï;j;k;l;m;n;o;ô;p;q;r;s;t;u;û;ù;v;w;x;y;z;1; ;?;';(;)"
    .split(';');
function simpleCrypt(inStr) {
  var outStr = "";
  for (i = 0; i < inStr.length; i++) {
    outStr += cryptTo[cryptFrom.indexOf(inStr.charAt(i))];
  }
  return outStr;
}
function simpleDecrypt(inStr) {
  var outStr = "";
  for (i = 0; i < inStr.length; i++) {
    outStr += cryptFrom[cryptTo.indexOf(inStr.charAt(i))];
  }
  return outStr;
}

function formatSmallButton(classname) {
  var result="<span class='roundedButtonSmall' style='top:0px;display:inline-block;width:16px;height:16px;'><div class='iconButton"+classname+"16' style='' >&nbsp;</div></span>";
  return result;
}

if (document.addEventListener) {
  var keys = [];
  var konami = "38,38,40,40,37,39,37,39,66,65";
  document.addEventListener("keydown", function(e) {
    keys.push(e.keyCode);
    if (konami.indexOf(keys.toString()) == 0) {
      if (keys.toString().indexOf(konami) >= 0) {
        var rnd = Math.floor(Math.random() * konamiMsg.length);
        showInfo(simpleDecrypt(konamiMsg[rnd]));
        keys = [];
      }
      ;
    } else {
      keys = [];
    }
  }, true);
};


var konamiMsg = [ '5ùhmuôçYgluêYuôYgluû',
    'FhmjïmhçuàljYufhXYklYuïmRgXuhguYkluêYufYçêêYmjuû', 'NcRluYêkYuû',
    'NcRluXçXurhmuYqîYVluû' ];
