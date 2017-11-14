@extends('layouts.app')
@push('styles')
    <link type="text/css" href="/extjs/css/ext-theme-gray/ext-theme-gray-all.css" rel="stylesheet" />
@endpush
@section('content')
    <div id="resultsGrid"></div>
@endsection
{{--http://www.techzoo.org/web-programming/php-programming/extjs-4-and-laravel-4-crud-tutorial.html--}}
@push('scripts')
    <script type="text/javascript" src="/extjs/ext-all.js"></script>
    <script type="text/javascript" src="/extjs/monthfield.js"></script>
    <script type="text/javascript" src="/extjs/ext-lang-ru.js"></script>
    <script>
        Ext.Loader.setConfig({enabled: true});
        Ext.Loader.setPath('Ext.ux', '{{asset('/extjs/ux')}}');
        Ext.require(['*','Ext.ux.grid.FiltersFeature','Ext.window.*','Ext.ux.statusbar.StatusBar']);

        var resultsStore = Ext.create('Ext.data.Store', {
            autoLoad: true,
            storeId: 'dataStore',
            fields: ['id', 'title','description','city_id','trainer_id'],
            pageSize: 10,
            remoteSort: true,
            autoSync: true,
            proxy: {
                type: 'ajax',
                api: {
                    read: '/trainings/ajax?action=get_results'
                },
                reader: {
                    type: 'json',
                    root: 'data',
                    idProperty: 'id',
                    totalProperty: 'total',
                    successProperty: 'success',
                    messageProperty: 'errors'
                },
                writer: {
                    type: 'json',
                    writeAllFields: true,
                    allowSingle: false,
                    encode: true,
                    root: 'data',
                    successProperty: 'success'
                },
                listeners: {
                    exception: function(proxy, response, options){
                        if (response.status == 401) location.reload();
                        else alert('Ошибка', response.status + ": " + response.statusText);
                    }
                }
            },
            sorters: [{property: 'id', direction: 'DESC'}]
        });

        Ext.onReady(function(){
            Ext.create('Ext.grid.Panel', {
                id: 'resultsGrid',
                store: Ext.data.StoreManager.lookup('dataStore'),
                anchor: '100% 100%',
                height: 250,
                renderTo: 'resultsGrid',
                columnLines: true,
                features: [{
                    ftype: 'filters',
                    encode: false,
                    local: false,
                    hideLabel: true,
                    filters: []
                }],
                viewConfig: {
                    loadMask: {
                        msg: 'загрузка...'
                    },
                    enableTextSelection: true,
                    getRowClass: function(record, index) {
                        if(record.data.status == '4' || record.data.status == '5') return 'delayed';
                    }
                },
                columns: [
                    { text: 'ID', width: 50, dataIndex: 'id', filter: { type: 'int' }, hidden: false },
                    { text: 'Название', minWidth: 180, dataIndex: 'title', filter: { type:'int', hideLabel: true }},
                    { text: 'Описание', minWidth: 210, dataIndex: 'description', filter: { type:'string', hideLabel: true }},
                    { text: 'Город', minWidth: 130, dataIndex: 'city_id', filter: { type:'int', hideLabel: true }},
                    { text: 'Трейнер', width: 80, dataIndex: 'trainer_id', filter: {type: 'int', hideLabel: true}},
                    {
                        text: 'Действия',
                        xtype: 'actioncolumn',
                        width: 75,
                        menuDisabled: true,
                        align: 'right',
                        sortable: false,
                        items: [
                            {
                                iconCls: 'preview-icon',
                                tooltip: 'Просмотр приглашение',
                                getClass: this.getActionClass,
                                handler: function(grid, rowIndex, colIndex){
                                    var record = grid.getStore().getAt(rowIndex);
                                    window.open('/modules/index.php/training/view_invite/'+record.get('id'), '_blank');
                                }
                            },
                            {
                                iconCls: 'edit-icon',
                                tooltip: 'Редактировать',
                                getClass: this.getActionClass,
                                handler: function(grid, rowIndex, colIndex){
                                    var record = grid.getStore().getAt(rowIndex);
                                    Training.edit_training(record.get('id'));
                                }
                            },
                            {
                                iconCls: 'delete-icon',
                                tooltip: 'Удалить',
                                getClass: this.getActionClass,
                                handler: function(grid, rowIndex, colIndex){
                                    var record = grid.getStore().getAt(rowIndex);

                                    Ext.MessageBox.confirm('Удалить наставника', '<b>'+record.get('title')+'</b> будет удален! Продолжить?', function(v){
                                        if(v != 'yes') return false;
                                        Training.delete_training(record.get('id'), function(){
                                            grid.getStore().remove(record);
                                        });
                                    });
                                }
                            }]
                    }
                ],
                bbar: [
                    Ext.create('Ext.PagingToolbar', {
                        pageSize: 10,
                        store: Ext.data.StoreManager.lookup('dataStore'),
                        displayInfo: true
                    })],
                tbar: [
                    {
                        xtype: 'numberfield',
                        emptyText: 'id',
                        id: 'idTextField',
                        width: 50,
                    },
                    {
                        xtype: 'textfield',
                        emptyText: 'Найти по Название',
                        id: 'titleTextField',
                        width: 230,
                        listeners: {
                            change: function(data, record){
                                var value = record;
                                var grid = Ext.getCmp('resultsGrid'),
                                    filter = grid.filters.getFilter('title');
                                if (!filter) {
                                    filter = grid.filters.addFilter({
                                        active: true,
                                        type: 'string',
                                        dataIndex: 'title'
                                    });

                                    filter.menu.show();
                                    filter.setValue(value);
                                    filter.menu.hide();
                                } else {
                                    filter.setValue(value);
                                    filter.setActive(true);
                                }
                            }
                        }
                    },
                    '->',
                    {
                        xtype: 'button',
                        text: 'Сбросить фильтры',
                        handler: function(){
                            Ext.getCmp('titleTextField').reset();
                            Ext.getCmp('resultsGrid').filters.clearFilters();
                        }
                    }
                ]
            });

        });
    </script>
@endpush
