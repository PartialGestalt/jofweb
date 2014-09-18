// ////////////////////////////////////////////////////////////////////////
//
// @file skin.js
// 
// @brief Javascript implementation of skin support utilities
//
// @details 
// This file implements common skin-specific client-side tools and utilities
//
// ////////////////////////////////////////////////////////////////////////

// All modules have a name to identify themselves.
var ak_module='skin-agua';

// ////////////////////////////////////////////////////////////////////////
// skin additional properties
//
// Add a couple of extra properties to some element types to help with
// skinning.
HTMLDivElement.prototype.popdownTimer = null;
HTMLLIElement.prototype.childMenu = null;

// ////////////////////////////////////////////////////////////////////////
// skin_menuItemEnter() -- Handle a mouseover on a menuitem
//
// @return Nothing
//
// We check to see if a menuitem has a child, and -- if so, pop it up
//
function skin_menuItemEnter(ele)
{
    var popmenu;
    var newTop,newLeft;
    /* Step 1: Do we have a DIV child? */
    try {
        /*  Step 1.1: Look it up if not cached */
            /* CLEAN: This just finds the last DIV child;
             * perhaps we should be more careful here? 
             */
        if (null == ele.childMenu) {
            for (kid in ele.childNodes) {
                if (ele.childNodes[kid].nodeName == 'DIV') {
                    ele.childMenu=ele.childNodes[kid];
                }
            }
        }
    } catch(err) {};
    if (null==ele.childMenu) return;
    popmenu = ele.childMenu;
       
    /* Step 2: Prep dimensions of popup */
        /* Step 2.1: Make it visible */
    popmenu.style.display = 'block';
        /* Step 2.2: Make sure it is at least as wide as item */
    if (popmenu.clientWidth < (ele.offsetWidth+2)) {
        popmenu.style.width = ''+(ele.offsetWidth+2)+'px';
    }

    /* Step 3: Calculate position based on parent class */
    switch (ele.className) {
        case 'top-nav': { /* Main navbar, left side */
            newTop = ele.offsetTop + ele.offsetHeight - 4;
            newLeft = ele.offsetLeft - ((popmenu.clientWidth-ele.offsetWidth)/2);
            break;
        }
        case 'top-nav-meta': { /* Main navbar, right side */
            newTop = ele.offsetTop + ele.offsetHeight - 4;
            newLeft = ele.offsetLeft - ((popmenu.clientWidth-ele.offsetWidth)/2);
            break;
        }
        default: { /* WTF? */
            newTop = ele.offsetTop + ele.offsetHeight;
            newLeft = ele.offsetLeft - 1;
            break;
        }
    }


    /* Step 3: Reposition, if necessary */
    ak_moveElement(popmenu,newTop,newLeft);
    
    return;
}

// ////////////////////////////////////////////////////////////////////////
// skin_menuItemExit() -- Handle a mouseout on a menuitem
//
// @return Nothing
//
// We check to see if a menuitem has a child, and -- if so, pop it down.
//
function skin_menuItemExit(ele)
{
    var popmenu=ele.childMenu;

    /* Step 1: Do we have a childmenu? */
    if (null == popmenu) return;
       
    /* Step 2: Simple hide */
    popmenu.style.display='none';
}

function skin_notebookTabEnter(ele) { };
function skin_notebookTabExit(ele) { };

// ////////////////////////////////////////////////////////////////////////
// skin_notebookTabClick() -- Handle a mouse click on a notebook tab
//
// @return Nothing
//
// Change the styling on page and tab
//
function skin_notebookTabClick(ele) 
{
    // Call common handler
    ak_notebookTabClick(ele);
}
