/**********************************************************************************************
 * JAFFA - Java Application Framework For All - Copyright (C) 2008 JAFFA Development Group
 *
 * This library is free software; you can redistribute it and/or modify it under the terms
 * of the GNU Lesser General Public License (version 2.1 any later).
 *
 * See http://jaffa.sourceforge.net/site/legal.html for more details.
 *********************************************************************************************/

/** Based on Original Work found at http://extjs.com/forum/showthread.php?p=203828#post203828
 *
 * @author chander, PaulE
 */
Ext.namespace("Ext.ux.grid");

Ext.ux.grid.MultiGroupingPanel = function(config) {
    config = config||{};
    config.tbar = new Ext.Toolbar({id:'grid-tbr'}); //FIXME is this hardcoded id needed?
    Ext.ux.grid.MultiGroupingPanel.superclass.constructor.call(this, config);
    //console.debug("Create MultiGroupingPanel",config);
};
Ext.extend(Ext.ux.grid.MultiGroupingPanel, Ext.grid.GridPanel, {

   initComponent : function(){
     //console.debug("MultiGroupingPanel.initComponent",this);
     Ext.ux.grid.MultiGroupingPanel.superclass.initComponent.call(this);
     
     // Initialise DragZone
     this.on("render", this.setUpDragging, this);
   }
    
  ,setUpDragging: function() {
     //console.debug("SetUpDragging", this);
     this.dragZone = new Ext.dd.DragZone(this.getTopToolbar().getEl(), {
       ddGroup:"grid-body" + this.getGridEl().id //FIXME - does this need to be unique to support multiple independant panels on the same page
      ,panel:this 
      ,scroll:false
       // @todo - docs
      ,onInitDrag : function(e) {
         // alert('init');
         var clone = this.dragData.ddel;
         clone.id = Ext.id('ven'); //FIXME??
         // clone.class='x-btn button';
         this.proxy.update(clone);
         return true;
       }

       // @todo - docs
      ,getDragData: function(e) {
         var target = Ext.get(e.getTarget().id);
         if(target.hasClass('x-toolbar x-small-editor')) {
           return false;
         }
           
         d = e.getTarget().cloneNode(true);
         d.id = Ext.id();
         console.debug("getDragData",this, target);
           
         this.dragData = {
           repairXY: Ext.fly(target).getXY(),
           ddel: d,
           btn:e.getTarget()
         };
         return this.dragData;
       }

       //Provide coordinates for the proxy to slide back to on failed drag.
       //This is the original XY coordinates of the draggable element.
      ,getRepairXY: function() {
         return this.dragData.repairXY;
       }
     });
     
     // This is the target when columns are dropped onto the toolbar (ie added to the group)
     this.dropTarget2s = new Ext.dd.DropTarget(this.getTopToolbar().getEl(), {
       ddGroup: "gridHeader" + this.getGridEl().id
      ,panel:this 
      ,notifyDrop: function(dd, e, data) {
         console.debug("Adding Filter", data);
         var btname= this.panel.getColumnModel().getDataIndex( this.panel.getView().getCellIndex(data.header));
         this.panel.store.groupBy(btname);
         return true;
       }
     });

     // This is the target when columns are dropped onto the grid (ie removed from the group)
     this.dropTarget22s = new Ext.dd.DropTarget(this.getView().el.dom.childNodes[0].childNodes[1], {
       ddGroup: "grid-body" + this.getGridEl().id  //FIXME - does this need to be unique to support multiple independant panels on the same page
      ,panel:this 
      ,notifyDrop: function(dd, e, data) {
         var txt = Ext.get(data.btn).dom.innerHTML;
         var tb = this.panel.getTopToolbar();
         console.debug("Removing Filter", txt);
         var bidx = tb.items.findIndexBy(function(b) {
           console.debug("Match button ",b.text);
           return b.text==txt;
         },this);
         console.debug("Found matching button", bidx);
         if(bidx<0) return; // Error!
         var fld = tb.items.get(bidx).fieldName;
         
         // Remove from toolbar
         Ext.removeNode(Ext.getDom(tb.items.get(bidx).id));
         if(bidx>0) Ext.removeNode(Ext.getDom(tb.items.get(bidx-1).id));;

         console.debug("Remove button", fld);
         //console.dir(button);
         var cidx=this.panel.view.cm.findColumnIndex(fld);
         
         if(cidx<0)
           console.error("Can't find column for field ", fld);
         
         this.panel.view.cm.setHidden(cidx, false);

         //Ext.removeNode(Ext.getDom(data.btn.id));

         // Remove this group from the groupField array
         // @todo - replace with method on store
         // this.panel.store.removeGroupField(fld);
         var temp=[];
         for(var i=this.panel.store.groupField.length-1;i>=0;i--) {
           if(this.panel.store.groupField[i]==fld) {
             this.panel.store.groupField.pop();
             break;
           }
           temp.push(this.panel.store.groupField[i]);
           this.panel.store.groupField.pop();
         }

         for(var i=temp.length-1;i>=0;i--) {
           this.panel.store.groupField.push(temp[i]);
         }

         if(this.panel.store.groupField.length==0)
           this.panel.store.groupField=false;

         this.panel.store.fireEvent('datachanged', this);
         return true;
       }
     }); 
    }
});
