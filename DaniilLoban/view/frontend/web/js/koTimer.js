define(['jquery', 'uiComponent'], function($, Component) {
  return Component.extend({
    defaults: {
        currentSeconds: 0,
        currentState: 'stop',
        formatedTime: '00:00:00',
        interval: null
    },
    initObservable: function () {
        this._super();
        this.observe(['currentSeconds', 'currentState', 'interval', 'formatedTime']);
        return this;
    },
    initialize: function () {
        this._super();
        this.currentState.subscribe(this.handleChangeState.bind(this));
        this.currentSeconds.subscribe(this.handleChangeTime.bind(this));
    },
    handleChangeTime: function (seconds){
        this.formatedTime(this._formatSecondsToTime(seconds))
    },
    handleChangeState: function (state) {
        switch (state) {
            case 'start':
                this._runTimer();
                break;
            case 'stop':
                this._clearInterval();
                this._resetTime();
                break;
            case 'pause':
                this._clearInterval();
                break;
            default:
                break;
        }
      },
    
    _formatSecondsToTime: function (sec) {
        const secNum = parseInt(sec, 10);
        const hours = Math.floor(secNum / 3600);
        const minutes = Math.floor((secNum - (hours * 3600)) / 60);
        const seconds = secNum - (hours * 3600) - (minutes * 60);
        const zeroComplete = (num) => ((num < 10) ? `0${num}` : num);
        return `${zeroComplete(hours)}:${zeroComplete(minutes)}:${zeroComplete(seconds)}`;
    },
    _runTimer: function () {
        this._clearInterval();
        this.interval(setInterval(() => {
            this.currentSeconds(this.currentSeconds() + 1)
        }, 1000))
    },
    _resetTime: function () {
        this.currentSeconds(0);
    },
    _hasInterval: function () {
        return this.interval();
    },
    _clearInterval: function() {
        if (this._hasInterval()) clearInterval(this.interval());
    },
    startTimer: function () {
        if (this.currentState() === 'start') {
            this._resetTime();
        }
        this.currentState('start')
    },
    stopTimer: function() {
        this.currentState('stop')
    },
    pauseTimer: function() {
        switch (this.currentState()) {
            case 'start':
                this.currentState('pause');
                break;
            case 'pause':
                this.currentState('start');
                break;
            default:
                break;
        }
    }
  })
});