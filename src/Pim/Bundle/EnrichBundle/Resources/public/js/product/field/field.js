"use strict";

define([
        'backbone',
        'underscore',
        'text!pim/template/product/field/field',
        'pim/attribute-manager',
        'pim/i18n'
        ], function (Backbone, _, fieldTemplate, AttributeManager, i18n) {
    var FieldModel = Backbone.Model.extend({
        'values': []
    });

    return Backbone.View.extend({
        tagName: 'div',
        className: 'field-container',
        attribute: null,
        fieldType: 'text',
        context: {},
        model: FieldModel,
        template: _.template(fieldTemplate),
        elements: {},
        editable: true,
        enabled: true,
        valid: true,
        initialize: function(attribute)
        {
            this.attribute    = attribute;
            this.model        = new FieldModel();
            this.elements     = {};
            this.context      = {};

            return this;
        },
        render: function()
        {
            this.getTemplateContext().done(_.bind(function(templateContext) {
                this.$el.empty();

                this.$el.html(this.template(templateContext));
                this.$('.form-field:not(".view") .field-input').append(this.renderInput(templateContext));

                _.each(this.elements, _.bind(function (elements, position) {
                    var $container = this.$('.' + position + '-elements-container');
                    $container.empty();
                    _.each(elements, _.bind(function(element) {
                        if (typeof element.render === 'function') {
                            $container.append(element.render().$el);
                        } else {
                            $container.append(element);
                        }
                    }, this));
                }, this));
                this.delegateEvents();
            }, this));

            return this;
        },
        renderInput: function() {
            throw new Error('You should implement your field template');
        },
        getTemplateContext: function() {
            var promise = $.Deferred();

            promise.resolve({
                type: this.fieldType,
                label: this.attribute.label[this.context.locale],
                value: this.getCurrentValue(),
                context: this.context,
                attribute: this.attribute,
                info: this.elements,
                editMode: this.getEditMode(),
                i18n: i18n
            });

            return promise.promise();
        },
        updateModel: function() {
            this.valid = true;
        },
        getData: function()
        {
            if (this.editable && this.enabled) {
                return this.model.get('values');
            } else {
                return [];
            }
        },
        setValues: function(values)
        {
            if (values.length === 0) {
                values.push(AttributeManager.getValue(
                    [],
                    this.attribute,
                    this.context.locale,
                    this.context.scope
                ));
            }

            this.model.set('values', values);
        },
        setContext: function(context)
        {
            this.context = context;

            this.render();
        },
        addElement: function(position, code, element) {
            if (!this.elements[position]) {
                this.elements[position] = {};
            }
            this.elements[position][code] = element;

            this.render();
        },
        removeElement: function(position, code) {
            if (this.elements[position] && this.elements[position][code]) {
                delete this.elements[position][code];
            }

            this.render();
        },
        setValid: function(valid)
        {
            this.valid = valid;
        },
        getValid: function()
        {
            return this.valid;
        },
        complete: function()
        {
            return true;
        },
        setEditable: function(editable) {
            this.editable = editable;
        },
        getEditable: function() {
            return this.editable;
        },
        setEnabled: function(enabled) {
            this.enabled = enabled;
        },
        getEnabled: function() {
            return this.enabled;
        },
        getEditMode: function()
        {
            if (this.editable) {
                if (this.enabled) {
                    return 'edit';
                } else {
                    return 'disabled';
                }
            } else {
                return 'view';
            }
        },
        getCurrentValue: function()
        {
            return AttributeManager.getValue(
                this.model.get('values'),
                this.attribute,
                this.context.locale,
                this.context.scope
            );
        },
        getEmptyData: function() {
            return null;
        },
        setCurrentValue: function(value)
        {
            var values = this.model.get('values');
            var productValue = this.getCurrentValue();

            productValue.value = value;
            this.model.set('values', values);
        }
    });
});
