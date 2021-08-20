define(["ko", "jquery", "uiComponent", "mage/url"], function(ko, $, Component, urlBuilder) {
    var componentMixin = {
        handleAutocomplete: function (searchValue) {
            if (this.isChoosing()) return;
            if (searchValue.length >= 5) {
                this.isLoading(true)
                $.getJSON(`${this.searchUrl}?sku=${searchValue}`, function (data) {
                    this.searchResult(data.list)
                    this.isLoading(false)
                }.bind(this));
            } else {
                this.searchResult([])
            }
        }
    };
    return function (targetComponent) {
        return targetComponent.extend(componentMixin);
    }
});