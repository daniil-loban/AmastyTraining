define(['ko', 'jquery', 'uiComponent', 'mage/url'], function(ko, $, Component, urlBuilder) {
    var componentMixin = {
        defaults: {
            searchText: '',
            searchUrl: urlBuilder.build("daniilloban/ajax/autocomplete"),
            searchResult: [],
            isLoading: false,
            isChoosing: false,
            selectedIndex: -1
        },
        initObservable: function () {
            this._super();
            this.observe(['searchText', 'searchResult', 'isLoading', 'isChoosing', 'selectedIndex']);
            return this;
        },
        initialize: function () {
            this._super();
            this.searchText.subscribe(this.handleAutocomplete.bind(this));
            this.onClickList = this.onClickList.bind(this);
        },
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