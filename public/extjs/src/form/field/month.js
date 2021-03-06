//MonthPicker
Ext.define('Ext.ux.form.field.Month',{

     extend        : 'Ext.form.field.Date'
    ,alias        : ['widget.monthfield']
    ,format        : 'm/Y'
    ,createPicker : function()
    {
        var  me        = this
            ,picker    = me.monthPicker;

        if(!picker)
        {
            me.monthPicker = picker = new Ext.picker.Month({
                 ownerCt    : me.ownerCt
                ,renderTo    : document.body
                ,floating    : true
                ,shadow        : false
                ,small        : me.showToday === false
                ,listeners : {
                     scope            : me
                    ,cancelclick    : me.onCancelClick
                    ,okclick        : me.onOkClick
                    ,yeardblclick    : me.onOkClick
                    ,monthdblclick    : me.onOkClick
                }
            });

            if(!me.disableAnim)
            {
                picker.hide();
                me.isExpanded = false;
            }

            me.on('beforehide', Ext.Function.bind(me.hideMonthPicker, me, [false]));
        }

        return picker;
    }
    ,onOkClick : function(picker, value)
    {
        var  me        = this
            ,month    = value[0]
            ,year    = value[1]
            ,date    = new Date(year, month, 1);
        
        if(date.getMonth() !== month)
        {
            date = Ext.Date.getLastDateOfMonth(new Date(year, month, 1));
        }
        me.activeDate = date = Ext.util.Format.date(date, me.format);
        me.setValue(date);
        me.hideMonthPicker();
    }
    ,onCancelClick : function()
    {
        this.setValue(this.activeDate);
        this.hideMonthPicker();
    }

    ,hideMonthPicker : function(animate)
    {
        var  me        = this
            ,picker    = me.picker;

        if(picker)
        {
            if(me.shouldAnimate(animate))
            {
                me.runAnimation(true);
            }
            else
            {
                picker.hide();
                me.isExpanded = false;
            }
        }

        return me;
    }
    ,shouldAnimate : function(animate)
    {
        return Ext.isDefined(animate) ? animate : !this.disableAnim;
    }
    ,runAnimation : function(isHide)
    {
        var     me        = this
            ,picker    = this.picker
            ,options = {
                 duration : 200
                ,callback : function()
                {
                    if(isHide)
                    {
                        picker.hide();
                        me.isExpanded = false;
                    }
                    else
                    {
                        picker.show();
                        me.isExpanded = true;
                    }
                }
            };
        if(isHide)
        {
            picker.el.slideOut('t', options);
        }
        else
        {
            picker.el.slideIn('t', options);
        }
    }
});