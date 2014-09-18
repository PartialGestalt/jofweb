// ////////////////////////////////////////////////////////////////////////
//
// @file util-core.js
// 
// @brief Javascript implementation of basic support utilities
//
// @details 
// This file implements common client-side tools and utilities.
//
// ////////////////////////////////////////////////////////////////////////

// All modules have a name to identify themselves.
var ak_module='util-core';
var ak_cookiePrefix='jof-';


//#include "util-menu.js.h"


// ////////////////////////////////////////////////////////////////////////
// ak_setCookie() -- Set a cookie
//
// @param cName Name of the cookie to set
// @param cValue Value of the cookie to set
// @param cDays Number of days the cookie should be valid (0 for "forever")
//
// @return Nothing
//
// NOTE: For our purposes, "forever" is ~10 years
// NOTE: We expect the input to be urlencoded already.
function ak_setCookie(cName,cValue,cDays)
{
    /* Step 1: calculate expiry time */
    var edays=cDays;
    var etime=new Date();
    if ((cDays == null) || (cDays==0)) { edays=3650; }
    etime.setDate(etime.getDate()+edays);

    /* Step 2: set the cookie name */
    if (cName.substr(0,ak_cookiePrefix.length) != ak_cookiePrefix) {
        cName=ak_cookiePrefix.concat(cName);
    }

    /* Step 3: actually set the cookie */
    document.cookie=cName+'='+cValue+';expires='+etime.toUTCString();
}

// ////////////////////////////////////////////////////////////////////////
// ak_spinner() -- Show a spinner in a div
//
// @param divName Name (id) of the div to spin
// @param skinName Name of the active skin
//
// @return Nothing
//
function ak_spinner(divName,skinName)
{
    /* Step 1: build spinner href */
    var spinref="/skins/"+skinName+"/spinner.gif";

    /* Step 2: find element */
    var spindiv=document.getElemementById(divName);
    
    /* Step 3: Replace innerHTML */
    try {
        spindiv.innerHTML='<img src="'+spinref+'" alt="Waiting..."/>';
    } catch (err) {}
}


// ////////////////////////////////////////////////////////////////////////
// ak_moveElement() -- Move an element to the given coordinates
//
// @return Nothing
//
// NOTE: The main trick behind moving elements is that the style
// coordinate properties ('top','left') are _strings_ and must have
// unit specifications.
//
function ak_moveElement(ele,top,left)
{
    ele.style.top = ''+top+'px';
    ele.style.left = ''+left+'px';
}

// ////////////////////////////////////////////////////////////////////////
// ak_hideElementById() -- Hide an element, given its DOM id
//
// @return Nothing
//
function ak_hideElementById(id)
{
    var ele = document.getElementById(id);

    try {
        ele.style.display = 'none';
    } catch (err) {}
}

// ////////////////////////////////////////////////////////////////////////
// ak_notebookTabClick() -- Execute a click on a notebook tab
//
// @return Nothing
//
function ak_notebookTabClick(ele)
{
    var idx;
    var kid;
    var notebook;
    var active;
    /* Step 1: Change tabs */
    for (idx in ele.parentNode.children) 
    {
        kid = ele.parentNode.children[idx];
        if (kid == ele)  {
            kid.className = 'notebook-tab-active';
        } else {
            kid.className = 'notebook-tab';
        }
    }
    /* Step 2: Switch page */
        /* Step 2.1: Get tokens */
    notebook = ele.parentNode.parentNode;
    active = ele.id.replace(notebook.id+'-tab-','');
    for (idx in notebook.children) 
    {
        kid = notebook.children[idx];
        try {
            if (kid.id.match(notebook.id+'-page-')) {
                if (kid.id.match(notebook.id + '-page-' + active)) {
                    kid.className = 'notebook-page-active';
                } else {
                    kid.className = 'notebook-page';
                }
            }
        } catch (err) { }
    }
}

// ////////////////////////////////////////////////////////////////////////
// svc_createXHR() -- Create a new XMLHttpRequest object
//
// @return Nothing
// NOTE: Eventually, we'll be able to trash this, but for now we support
//       older IE versions with the ActiveX hack.
//
function svc_createXHR(svc_url)
{
    var xhr;
    try {
        /* Step 1: Create base object */
        if (window.XMLHttpRequest) xhr = new XMLHttpRequest();
        else xhr = new ActiveXObject("Microsoft.XMLHTTP");

        /* Step 2: Init/open */
        xhr.open("GET",svc_url,true);
    } catch(err) {
        /* Anything goes wrong -- bail */
        return null;
    }

    /* Step 3: return it */
    return xhr;
}

// ////////////////////////////////////////////////////////////////////////
// svc_loadDiv() -- Load a DIV with the output from a service request (XHR)
//
// @return Nothing
//
function svc_loadDiv(divId,service,params)
{
    var svc_url;
    var xhr;
    var div;

    /* Step 1: DIV sanity check */
    div = document.getElementById(divId);
    if (null == div) return;

    /* Step 2: build service URL */
    svc_url='svc-'+service+'.php';
    if (null != params) svc_url+='?'+params;

    /* Step 3: Create/setup our XHR */
    xhr = svc_createXHR(svc_url);

    /* Step 4: Setup handler */
    xhr.onreadystatechange = function() {
        if ((this.readyState == 4) && (this.status == 200)) {
            div.innerHTML=this.responseText;
        }
    }

    /* Step 4: Kick off the request */
    xhr.send();
}

// ////////////////////////////////////////////////////////////////////////
// svc_loadDivIFrame() -- Load a DIV with an IFrame
//
// @return Nothing
//
function svc_loadDivIFrame(divId,service,params)
{
    var svc_url;
    var div;

    /* Step 1: DIV sanity check */
    div = document.getElementById(divId);
    if (null == div) return;

    /* Step 2: Build service URL */
    svc_url='svc-'+service+'.php';
    if (null != params) svc_url+='?'+params;

    /* Step 3: Write IFRAME tag into div */
    div.innerHTML = '<iframe id="' + divId + '-iframe" name="' + divId + '-iframe" frameborder="0" width="100%" height="400px" scrolling="auto" src="' + svc_url + '" onload="svc_resizeDivIFrame(\'' + divId + '\');"/>';
}

// ////////////////////////////////////////////////////////////////////////
// svc_resizeDivIFrame() -- Resize a parent IFRAME to match our document contents.
//
// @return Nothing
//
function svc_resizeDivIFrame(divId)
{
    var theIFrame;
    var needHeight;

    try {
    theIFrame = document.getElementById(divId + '-iframe');
    needHeight = 18+theIFrame.contentDocument.height;
    if (needHeight < 600) needHeight = 600; // Set min height
    theIFrame.style.height = '' + needHeight + 'px';
    theIFrame.height = needHeight;
    } catch (err) {}
}

// ////////////////////////////////////////////////////////////////////////
// svc_formParameters() -- Generate a parameterized string from a form's 
//                         fields.
//
// @return String of parameters (i.e. <field1>=<value1>&<field2>=<value2>...)
//
function svc_formParameters(formId)
{
    var fields;
    var ret = '';
    /* Step 1: get form from DOM */
    try {
       fields = document.getElementById(formId).elements;
    } catch (err) {
        return ret;
    }
    /* Step 2: Build query strings from all fields */
        /* NOTE: Most fields are a simple value copy, but radio buttons
         * only contribute if they're checked.
         */
    try {
        for (i in fields) {
            if (fields[i].type == 'radio') {
                if (fields[i].checked == false) continue;
            }
            if (ret != '') ret+='&';
            ret += escape(fields[i].name) + '=' + escape(fields[i].value);
        }
    } catch (err) {
    }

    return ret;
}

// ////////////////////////////////////////////////////////////////////////
// AK_Form_Popup -- object class for form popups
//
// Attributes:
// itemID -- DOM id of the item to update 
// svcName -- name of the service to use to populate the popup
// svcParam -- if non-null, the parameter string to pass to the service
// div -- The div to use for the floating popup
//
function AK_Form_Popup(itemID,svcName,svcParam)
{
    /* Basic properties */
    this.itemID = itemID;
    this.svcName = svcName;
    /* new Document element, reused every time */
    this.div = document.createElement("div");
    this.div.id = itemID + "-popup";
    this.div.className = "form-popup";
    document.body.appendChild(this.div);
    /* preload div from service while invisible (faster popup) */
    try {
      this.svcParam = 'targetDiv='+this.div.id;
      if (null != svcParam) this.svcParam += svcParam;
      svc_loadDiv(this.div.id,svcName,this.svcParam);
    } catch (err) {}
}

// ////////////////////////////////////////////////////////////////////////
// akForm_Popups -- array of registered form popups, indexed by trigger id.
var akForm_Popups = new Array();

// ////////////////////////////////////////////////////////////////////////
// akForm_handlePopup -- Event handler for triggering a form popup
//
// @param ev Triggering event 
function akForm_handlePopup(evt)
{
    var pop;
    var ev=window.event;
    if (ev == null) ev = evt;

    /* Step 1: Lookup popup info from trigger id */
    pop = akForm_Popups[this.id];

    /* Step 2: Show the div (already loaded, so just position and popup */
    //pop.div.setAttribute("onclick","this.style.display=\"none\";");
    pop.div.style.position="fixed";
    pop.div.style.display = "inline";
    ak_moveElement(pop.div,ev.clientY,ev.clientX-pop.div.offsetWidth);

}

// ////////////////////////////////////////////////////////////////////////
// akForm_registerPopup -- bind a service-populated popup to a form element
//
// @param itemID -- DOM id of item to update with results (text or hidden field)
// @param triggerID -- DOM id of element to act as popup trigger
// @param svcName -- Name of service that will populate the popup
// @param svcParam -- Parameter string to pass to popup service
//
// @return Nothing, but may update itemID.value
//
function akForm_registerPopup(itemID,triggerID,svcName,svcParam)
{
    var trigger;

    try {
        /* Lookup trigger element */
        trigger = document.getElementById(triggerID);
        /* Add click handler */
        trigger.onclick=akForm_handlePopup;
        /* Register new popup object */
        akForm_Popups[triggerID] = new AK_Form_Popup(itemID,svcName,svcParam);
    } catch (err) {
        alert("registration error: " + err);
    }
}

// ////////////////////////////////////////////////////////////////////////
// akForm_datepickerNav
//
// @param divId -- DOM id of the div to update on date change
// @param refDate -- reference date for loading
//
// @return Nothing, but may update divID contents
//
function akForm_datepickerNav(divId,refDate)
{
    svc_loadDiv(divId,'form-date','refDate='+refDate+'&targetDiv='+divId);
}

// ////////////////////////////////////////////////////////////////////////
// akForm_datepickerDay
//
// @param divId -- DOM id of the div containing the datepicker
// @param refDate -- selected date
//
// @return
//
// The div ID is the target form element id with a '-popup' suffix;
// strip the suffix to find the target form item, and then update it's value;
//
function akForm_datepickerDay(divId,refDate)
{
    var textEle;
    var dateParts = refDate.split('-');
    
    // Step 1: Find the text field
    textEle = document.getElementById(divId.replace('-popup',''));
    try {
        textEle.value= dateParts[1] + '/' + dateParts[2] + '/' + dateParts[0];
    } catch (err) {};

    // Step 2: Close the popup
    ak_hideElementById(divId);
}

// ////////////////////////////////////////////////////////////////////////
// akForm_timepickerChange()
//
// @param rootId -- root of DOM ids that comprise the timepicker
//
// @return
//
// NOTES:
// The timepicker is actually 5 elements:
//   1. A hidden item '<rootId>' for storing the calculated time
//   2. An hour selector '<rootId>-hour'
//   3. A minute selector '<rootId>-minute'
//   4. An am/pm selector '<rootId>-ampm'
//   5. An explanation span, for indicating midnight or noon
//
// For our purposes, '12:00am' is midnight, and '12:00pm' is noon.
//
// We will create a 6-character time string (24hr notation), 'hhmmss', 
// by calculating a 7-digit number, convert to a string, and strip the
// leading '1'. This mechanism guarantees that we never have to pad
// any leading '0' after converting to a string.
//
function akForm_timepickerChange(rootId)
{
    var storage = document.getElementById(rootId);
    var hour    = document.getElementById(rootId+'-hour');
    var minute  = document.getElementById(rootId+'-minute');
    var ampm    = document.getElementById(rootId+'-ampm');
    var note    = document.getElementById(rootId+'-note');

    var calctime = 1000000; /* Force result to 7 digits */

    try {
        /* Step 1: Convert the hour from 12-hr+am/pm to 24-hr value */
            /* NOTE: The value of the am/pm field is the raw number of
             *       hours to add.
             */
        calctime += ((Number(hour.value))%12 + Number(ampm.value)) * 10000;

        /* Step 2: Incorporate minutes and seconds */
        calctime += Number(minute.value) * 100;

        /* Step 3: Special case indicators */
        switch (calctime) {
            case 1000000: note.innerHTML = '(midnight)'; break;
            case 1120000: note.innerHTML = '(noon)'; break;
            default: note.innerHTML = ''; break;
        }

        /* Step 4: Convert to a string, stripping the leading '1' */
        storage.value = calctime.toString().slice(1);

    } catch (err) { }

    return true;
}

// ////////////////////////////////////////////////////////////////////////
// akForm_handleGroupClick
//
// @param groupId -- ID of the group who's been clicked
//
// @return
//
// The groupId is the DOM id of the input checkbox that triggered us.
// The list of form items to show/hide is in a hidden span "<groupId>-storage"
// We actually show/hide the rows containing each of the component elements.
//
function akForm_handleGroupClick(groupId)
{
    /* Step 1: Prep the various elements */
    var dmode; // Display mode
    var kids;
    var item = document.getElementById(groupId);
    var store = document.getElementById(groupId + "-storage");
    if (!item || !store || !store.innerHTML) return;

    /* Step 2: Determine if this is hide/show */
    if (item.checked) {
        dmode = 'table-row';
        item.value = 'true';
    } else {
        dmode = 'none';
        item.value = 'false';
    }

    /* Step 3: Loop over stored values */
    kids = store.innerHTML.split(',');
    try {
        for (idx in kids) {
            kid = document.getElementById(kids[idx] + '-row');
            if (kid) kid.style.display = dmode;
        }
    } catch (err) {}
}
