define(['ko', 'jquery', 'uiComponent', 'mage/url'], function(ko, $, Component, urlBuilder) {
  return Component.extend({
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
    onClickList: function (item) {
        this.isChoosing(true);
        this.searchText(item.sku)
        this.searchResult([])
        this.isChoosing(false);
    },
    _getAutocomleteListItems: function (input) {
        var ul = Array.from($(input).parent().next().children(0).children())
        return ul;
    },
    onMouseEnter: function (data, event) {
        //this.onClickList(searchResult[0]);
        $(items[this.selectedIndex()])
            .removeClass('autocomplete-active')
    },
    onKeyUp: function (data, event) {
        var { keyCode } = event;
        if ([38 /* up */ , 40 /* down*/, 13 /* enter */].includes(keyCode)) {
            items = this._getAutocomleteListItems(event.target);
            if (items.length === 0) return;
            var currIndex = this.selectedIndex();
            if (currIndex >= 0 && currIndex < 20) {
                $(items[currIndex]).removeClass('autocomplete-active')    
            }
            if (keyCode === 13 && currIndex >= 0 && currIndex < 20) {
                this.onClickList(this.searchResult()[currIndex]);
            }
            if (keyCode === 40 && currIndex < 19) {
                this.selectedIndex(this.selectedIndex() + 1)
            }
            if (keyCode === 38 && currIndex > -1) {
                this.selectedIndex(this.selectedIndex() - 1)
            }
            $(items[this.selectedIndex()]).addClass('autocomplete-active')
        }
    },
    handleAutocomplete: function (searchValue) {
        if (this.isChoosing()) return;
        if (searchValue.length >= 3) {
            this.isLoading(true)
            $.getJSON(`${this.searchUrl}?sku=${searchValue}`, function (data) {
                this.searchResult(data.list)
                this.isLoading(false)
            }.bind(this));
        } else {
            this.searchResult([])
        }
    }
  })
});