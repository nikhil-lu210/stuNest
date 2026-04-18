/**
 * Home / marketing search bar — Alpine.js pickers for move-in date & guests.
 */
document.addEventListener('alpine:init', function () {
  Alpine.data('searchForm', function (config) {
    config = config || {};
    var strings = config.strings || {};
    return {
      moveIn: config.moveIn != null ? String(config.moveIn) : '',
      guests: Math.max(1, Math.min(10, parseInt(config.guests, 10) || 1)),
      dateOpen: false,
      guestsOpen: false,
      strings: strings,

      toggleDate: function () {
        this.dateOpen = !this.dateOpen;
        if (this.dateOpen) {
          this.guestsOpen = false;
        }
      },

      toggleGuests: function () {
        this.guestsOpen = !this.guestsOpen;
        if (this.guestsOpen) {
          this.dateOpen = false;
        }
      },

      moveInLabel: function () {
        var addDates = this.strings.addDates || 'Add dates';
        if (!this.moveIn) {
          return addDates;
        }
        try {
          var d = new Date(this.moveIn + 'T12:00:00');
          if (isNaN(d.getTime())) {
            return addDates;
          }
          return d.toLocaleDateString(undefined, {
            month: 'short',
            day: 'numeric',
            year: 'numeric',
          });
        } catch (e) {
          return addDates;
        }
      },

      guestsLabel: function () {
        var n = this.guests;
        var unit = n === 1 ? this.strings.student || 'student' : this.strings.students || 'students';
        return n + ' ' + unit;
      },

      adjustGuests: function (delta) {
        this.guests = Math.max(1, Math.min(10, this.guests + delta));
      },

      clearMoveIn: function () {
        this.moveIn = '';
      },
    };
  });
});
