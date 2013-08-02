
Ext.Loader.setConfig({
    enabled: true,
    paths: {
        'Ext': '/js/extjs/4.1.1/src'
    }
});


Ext.application({
    launch: function () {
        Ext.create('Ext.form.Panel', {
            id: 'custom-messagebox-form',
            items: [{
                xtype: 'container',
                layout: 'fit',
                html: '3つのフィールドに値を入力し、「ウィンドウ作成」ボタンを押すと、<br />'+
                    'フィールドの値をボタンにしたMessageBoxが表示されます。',
                margin: '0 0 50 0'
            }, {
                xtype: 'container',
                layout: 'hbox',
                defaults: {
                    xtype: 'textfield',
                    labelWidth: 90,
                    margin: '0 50 0 0',
                    width: 250,
                    listeners: {
                        specialkey: function (f, e) {
                            if (e.getKey() == e.ENTER) {
                                var form = this.up('form'),
                                    btn  = form.down('button[action="submit"]');
                                btn.handler();
                            }
                        }
                    }
                },
                items: [{
                    name: 'btn1',
                    fieldLabel: 'OKボタン',
                    allowBlank: false,
                    value: 'OK'
                }, {
                    name: 'btn2',
                    fieldLabel: 'YESボタン',
                    value: 'YES'
                }, {
                    name: 'btn3',
                    fieldLabel: 'CANCELボタン',
                    value: 'CANCEL'
                }]
            }],
            buttons: [{
                text: 'ウィンドウの生成',
                action: 'submit',
                scale: 'large',
                handler: function () {
                    var form = Ext.getCmp('custom-messagebox-form'),
                        btn1 = form.down('textfield[name="btn1"]'),
                        btn2 = form.down('textfield[name="btn2"]'),
                        btn3 = form.down('textfield[name="btn3"]');

                    if (form.getForm().isValid()) {
                        var buttons = {};

                        buttons.ok = btn1.getValue();

                        if (btn2.getValue() !== '') {
                            buttons.yes = btn2.getValue();
                        }

                        if (btn3.getValue() !== '') {
                            buttons.cancel = btn3.getValue();
                        }


                        Ext.Msg.show({
                            title: 'Information!',
                            msg: 'Custom MessageBox',
                            icon: Ext.Msg.INFO,
                            buttonText: buttons,
                            fn: function (btn) {
                                btn = btn.toUpperCase();

                                var el = Ext.get('clicked');
                                el.dom.innerHTML = '<p font-size="1.5em;">Ext.Msg.'+btn+' clicked!</p>';
                            }
                        });
                    }
                }
            }],
            border: false,
            buttonAlign: 'center',
            bodyStyle: 'padding: 30px',
            renderTo: 'render-component'
        });
    }
});
