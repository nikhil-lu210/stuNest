/**
 * Explore: filters (Alpine), Leaflet map, saved properties (heart).
 */
document.addEventListener('alpine:init', function () {
  Alpine.data('exploreFilterUi', function (initial, geo) {
    initial = initial || {};
    geo = geo || {};

    function arrFromInitial(key) {
      var v = initial[key];
      return Array.isArray(v) ? v.slice() : [];
    }

    return {
      filtersOpen: false,
      priceOpen: false,
      distanceOpen: false,
      citiesUrlPrefix: geo.citiesUrlPrefix || '',
      areasUrlPrefix: geo.areasUrlPrefix || '',
      cityOptions: Array.isArray(geo.initialCities) ? geo.initialCities.slice() : [],
      areaOptions: Array.isArray(geo.initialAreas) ? geo.initialAreas.slice() : [],

      rentPeriod: initial.rent_period != null ? String(initial.rent_period) : '',
      priceMin: initial.price_min != null ? String(initial.price_min) : '',
      priceMax: initial.price_max != null ? String(initial.price_max) : '',
      distanceMax: initial.distance_max != null ? String(initial.distance_max) : '',
      distanceTransitMax: initial.distance_transit_max != null ? String(initial.distance_transit_max) : '',
      countryId: initial.country_id != null ? String(initial.country_id) : '',
      cityId: initial.city_id != null ? String(initial.city_id) : '',
      areaId: initial.area_id != null ? String(initial.area_id) : '',
      propertyType: initial.property_type != null ? String(initial.property_type) : '',
      listingCategory: initial.listing_category != null ? String(initial.listing_category) : '',
      bedroomsMin: initial.bedrooms_min != null ? String(initial.bedrooms_min) : '',
      bedroomsMax: initial.bedrooms_max != null ? String(initial.bedrooms_max) : '',
      bathrooms: initial.bathrooms != null ? String(initial.bathrooms) : '',
      bedType: initial.bed_type != null ? String(initial.bed_type) : '',
      bathroomType: initial.bathroom_type != null ? String(initial.bathroom_type) : '',
      billsIncluded: initial.bills_included != null ? String(initial.bills_included) : '',
      minContractLength: initial.min_contract_length != null ? String(initial.min_contract_length) : '',
      depositRequired: initial.deposit_required != null ? String(initial.deposit_required) : '',
      rentFor: initial.rent_for != null ? String(initial.rent_for) : '',
      flatmateVibe: initial.flatmate_vibe != null ? String(initial.flatmate_vibe) : '',
      suitableFor: arrFromInitial('suitable_for'),
      houseRules: arrFromInitial('house_rules'),
      amenities: arrFromInitial('amenities'),

      ensuite: !!initial.ensuite,
      gym: !!initial.gym,
      bills: !!initial.bills,
      furnished: !!initial.furnished,
      wifi: !!initial.wifi,
      providesAgreement: !!initial.provides_agreement,

      closeAll: function () {
        this.priceOpen = false;
        this.distanceOpen = false;
      },

      toggleQuery: function (key) {
        var u = new URL(window.location.href);
        var cur = u.searchParams.get(key);
        if (cur === '1' || cur === 'true') {
          u.searchParams.delete(key);
        } else {
          u.searchParams.set(key, '1');
        }
        u.searchParams.delete('page');
        window.location.href = u.toString();
      },

      loadCities: function () {
        var self = this;
        var id = this.countryId;
        this.cityId = '';
        this.areaId = '';
        this.areaOptions = [];
        if (!id || !this.citiesUrlPrefix) {
          this.cityOptions = [];
          return;
        }
        fetch(this.citiesUrlPrefix + id, {
          headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
          credentials: 'same-origin',
        })
          .then(function (r) {
            return r.json();
          })
          .then(function (rows) {
            self.cityOptions = rows || [];
          })
          .catch(function () {
            self.cityOptions = [];
          });
      },

      loadAreas: function () {
        var self = this;
        var id = this.cityId;
        this.areaId = '';
        if (!id || !this.areasUrlPrefix) {
          this.areaOptions = [];
          return;
        }
        fetch(this.areasUrlPrefix + id, {
          headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
          credentials: 'same-origin',
        })
          .then(function (r) {
            return r.json();
          })
          .then(function (rows) {
            self.areaOptions = rows || [];
          })
          .catch(function () {
            self.areaOptions = [];
          });
      },

      applyPrice: function () {
        var u = new URL(window.location.href);
        var delIf = function (k, val) {
          if (val === '' || val == null) {
            u.searchParams.delete(k);
          } else {
            u.searchParams.set(k, String(val));
          }
        };
        var hasPrice =
          (this.priceMin !== '' && this.priceMin != null) || (this.priceMax !== '' && this.priceMax != null);
        if (this.rentPeriod) {
          u.searchParams.set('rent_period', this.rentPeriod);
        } else if (hasPrice) {
          u.searchParams.set('rent_period', 'week');
        } else {
          u.searchParams.delete('rent_period');
        }
        delIf('price_min', this.priceMin);
        delIf('price_max', this.priceMax);
        u.searchParams.delete('page');
        window.location.href = u.toString();
      },

      applyDistance: function () {
        var u = new URL(window.location.href);
        var delIf = function (k, val) {
          if (val === '' || val == null) {
            u.searchParams.delete(k);
          } else {
            u.searchParams.set(k, String(val));
          }
        };
        delIf('distance_max', this.distanceMax);
        delIf('distance_transit_max', this.distanceTransitMax);
        u.searchParams.delete('page');
        window.location.href = u.toString();
      },

      clearFilterParams: function () {
        var u = new URL(window.location.href);
        var next = new URL(u.origin + u.pathname);
        ['q', 'move_in', 'guests'].forEach(function (k) {
          var v = u.searchParams.get(k);
          if (v !== null && v !== '') {
            next.searchParams.set(k, v);
          }
        });
        window.location.href = next.toString();
      },

      applyAllFromPanel: function () {
        var cur = new URL(window.location.href);
        var base = new URL(cur.origin + cur.pathname);
        ['q', 'move_in', 'guests'].forEach(function (k) {
          var v = cur.searchParams.get(k);
          if (v !== null && v !== '') {
            base.searchParams.set(k, v);
          }
        });

        var setOrDel = function (k, val) {
          if (val === '' || val == null) {
            return;
          }
          base.searchParams.set(k, String(val));
        };

        var hasPrice =
          (this.priceMin !== '' && this.priceMin != null) || (this.priceMax !== '' && this.priceMax != null);
        if (this.rentPeriod) {
          base.searchParams.set('rent_period', this.rentPeriod);
        } else if (hasPrice) {
          base.searchParams.set('rent_period', 'week');
        }

        setOrDel('price_min', this.priceMin);
        setOrDel('price_max', this.priceMax);
        setOrDel('distance_max', this.distanceMax);
        setOrDel('distance_transit_max', this.distanceTransitMax);
        setOrDel('country_id', this.countryId);
        setOrDel('city_id', this.cityId);
        setOrDel('area_id', this.areaId);
        setOrDel('property_type', this.propertyType);
        setOrDel('listing_category', this.listingCategory);
        setOrDel('bedrooms_min', this.bedroomsMin);
        setOrDel('bedrooms_max', this.bedroomsMax);
        setOrDel('bathrooms', this.bathrooms);
        setOrDel('bed_type', this.bedType);
        setOrDel('bathroom_type', this.bathroomType);
        setOrDel('bills_included', this.billsIncluded);
        setOrDel('min_contract_length', this.minContractLength);
        setOrDel('deposit_required', this.depositRequired);
        setOrDel('rent_for', this.rentFor);
        setOrDel('flatmate_vibe', this.flatmateVibe);

        ['ensuite', 'gym', 'furnished', 'wifi', 'provides_agreement'].forEach(
          function (k) {
            if (this[k]) {
              base.searchParams.set(k, '1');
            }
          }.bind(this)
        );
        if (!this.billsIncluded && this.bills) {
          base.searchParams.set('bills', '1');
        }

        var self = this;
        (this.suitableFor || []).forEach(function (v) {
          if (v) {
            base.searchParams.append('suitable_for[]', v);
          }
        });
        (this.houseRules || []).forEach(function (v) {
          if (v) {
            base.searchParams.append('house_rules[]', v);
          }
        });
        (this.amenities || []).forEach(function (a) {
          if (a) {
            base.searchParams.append('amenities[]', a);
          }
        });

        this.filtersOpen = false;
        window.location.href = base.toString();
      },

      syncFromUrl: function () {
        var u = new URL(window.location.href);
        var g = function (k) {
          return u.searchParams.get(k) || '';
        };
        this.rentPeriod = g('rent_period');
        this.priceMin = g('price_min');
        this.priceMax = g('price_max');
        this.distanceMax = g('distance_max');
        this.distanceTransitMax = g('distance_transit_max');
        this.countryId = g('country_id');
        this.cityId = g('city_id');
        this.areaId = g('area_id');
        this.propertyType = g('property_type');
        if (u.searchParams.get('studio') === '1' && !this.propertyType) {
          this.propertyType = 'studio';
        }
        this.listingCategory = g('listing_category');
        this.bedroomsMin = g('bedrooms_min');
        this.bedroomsMax = g('bedrooms_max');
        this.bathrooms = g('bathrooms');
        this.bedType = g('bed_type');
        this.bathroomType = g('bathroom_type');
        this.billsIncluded = g('bills_included');
        this.minContractLength = g('min_contract_length');
        this.depositRequired = g('deposit_required');
        this.rentFor = g('rent_for');
        this.flatmateVibe = g('flatmate_vibe');
        this.ensuite = u.searchParams.get('ensuite') === '1';
        this.gym = u.searchParams.get('gym') === '1';
        this.bills = u.searchParams.get('bills') === '1';
        if (!this.billsIncluded && this.bills) {
          this.billsIncluded = 'all';
          this.bills = false;
        }
        this.furnished = u.searchParams.get('furnished') === '1';
        this.wifi = u.searchParams.get('wifi') === '1';
        this.providesAgreement = u.searchParams.get('provides_agreement') === '1';

        this.suitableFor = [];
        this.houseRules = [];
        this.amenities = [];
        var self = this;
        u.searchParams.forEach(function (val, key) {
          if (key === 'suitable_for[]' && val) {
            self.suitableFor.push(val);
          }
          if (key === 'house_rules[]' && val) {
            self.houseRules.push(val);
          }
          if (key === 'amenities[]' && val && val !== 'wifi' && val !== 'building_gym') {
            self.amenities.push(val);
          }
        });
      },
    };
  });
});

function exploreRefreshLucide() {
  if (window.lucide && typeof lucide.createIcons === 'function') {
    lucide.createIcons();
  }
}

function exploreInitMap(containerId, markers, options) {
  options = options || {};
  var el = document.getElementById(containerId);
  if (!el || typeof L === 'undefined') {
    return null;
  }
  if (el._leaflet_id) {
    return null;
  }

  var map = L.map(el, { scrollWheelZoom: true });

  L.tileLayer('https://{s}.basemaps.cartocdn.com/light_nolabels/{z}/{x}/{y}{r}.png', {
    attribution:
      '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> &copy; <a href="https://carto.com/">CARTO</a>',
    subdomains: 'abcd',
    maxZoom: 20,
  }).addTo(map);

  var bounds = [];
  var markerById = {};

  function resetPinStyles() {
    el.querySelectorAll('.explore-map-pin').forEach(function (inner) {
      inner.classList.remove('bg-gray-900', 'text-white', 'border-black');
      inner.classList.add('bg-white', 'text-gray-900', 'border-gray-200');
    });
  }

  (markers || []).forEach(function (m) {
    var lat = m.lat;
    var lng = m.lng;
    if (lat == null || lng == null) {
      return;
    }
    bounds.push([lat, lng]);

    var priceLabel = '€' + m.price;
    var icon = L.divIcon({
      className: 'explore-map-price-marker',
      html:
        '<div class="explore-map-pin px-2.5 py-1 rounded-full shadow-lg font-bold text-sm border border-gray-200 bg-white text-gray-900 whitespace-nowrap" data-marker-id="' +
        m.id +
        '">' +
        priceLabel +
        '</div>',
      iconSize: [64, 32],
      iconAnchor: [32, 16],
    });

    var marker = L.marker([lat, lng], { icon: icon }).addTo(map);
    markerById[m.id] = marker;

    if (m.url) {
      marker.bindPopup(
        '<a href="' +
          m.url +
          '" class="font-semibold text-gray-900 hover:underline">' +
          (m.title || 'Listing') +
          '</a><div class="text-sm text-gray-600 mt-1">' +
          priceLabel +
          '</div>'
      );
    }

    marker.on('click', function () {
      resetPinStyles();
      var pinEl = typeof marker.getElement === 'function' ? marker.getElement() : null;
      if (pinEl) {
        var inner = pinEl.querySelector('.explore-map-pin');
        if (inner) {
          inner.classList.remove('bg-white', 'text-gray-900', 'border-gray-200');
          inner.classList.add('bg-gray-900', 'text-white', 'border-black');
        }
      }
      var card = document.querySelector('[data-explore-property="' + m.id + '"]');
      if (card) {
        card.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        card.classList.add('ring-2', 'ring-gray-900', 'ring-offset-2');
        setTimeout(function () {
          card.classList.remove('ring-2', 'ring-gray-900', 'ring-offset-2');
        }, 1600);
      }
    });
  });

  if (bounds.length > 0) {
    map.fitBounds(bounds, { padding: [48, 48], maxZoom: 14 });
  } else {
    map.setView([20, 0], 2);
  }

  map._exploreMarkerById = markerById;
  return map;
}

window.initExploreMaps = function (markers) {
  var desktop = exploreInitMap('explore-map', markers, {});

  window.initExploreMapMobile = function () {
    var el = document.getElementById('explore-map-mobile');
    if (!el) {
      return;
    }
    if (window._exploreMobileMap) {
      window._exploreMobileMap.invalidateSize();
      return;
    }
    window._exploreMobileMap = exploreInitMap('explore-map-mobile', markers, {});
    if (window._exploreMobileMap && typeof window._exploreMobileMap.invalidateSize === 'function') {
      setTimeout(function () {
        window._exploreMobileMap.invalidateSize();
      }, 80);
    }
  };

  return { desktop: desktop };
};

function exploreSetupFavorites() {
  var token =
    document.querySelector('meta[name="csrf-token"]') &&
    document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  var holder = document.querySelector('[data-explore-favorite-prefix]');
  var prefix = holder && holder.getAttribute('data-explore-favorite-prefix');
  if (!prefix) {
    return;
  }

  document.querySelectorAll('.explore-heart-btn').forEach(function (btn) {
    btn.addEventListener('click', function (e) {
      e.preventDefault();
      e.stopPropagation();
      var id = btn.getAttribute('data-property-id');
      if (!id) {
        return;
      }
      var url = prefix + id;
      fetch(url, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': token || '',
        },
        credentials: 'same-origin',
        body: JSON.stringify({}),
      })
        .then(function (res) {
          if (res.status === 401) {
            return res.json().then(function (data) {
              var login = (data && data.login_url) || '/login';
              window.location.href = login;
            });
          }
          if (res.status === 503) {
            return res.json().then(function (data) {
              var msg = data && data.message;
              if (msg && typeof console !== 'undefined' && console.warn) {
                console.warn('[explore favorites]', msg);
              }
              return null;
            }).catch(function () {
              return null;
            });
          }
          if (!res.ok) {
            return res
              .text()
              .then(function (body) {
                if (typeof console !== 'undefined' && console.warn) {
                  console.warn('[explore favorites]', res.status, body || '');
                }
                throw new Error('Request failed');
              });
          }
          return res.json();
        })
        .then(function (data) {
          if (!data) {
            return;
          }
          var saved = !!data.saved;
          btn.setAttribute('data-saved', saved ? '1' : '0');
          // Lucide replaces <i data-lucide> with <svg>; querySelector('i') is always null after createIcons().
          var icon = btn.querySelector('svg') || btn.querySelector('i');
          btn.classList.toggle('text-red-500', saved);
          btn.classList.toggle('text-gray-900', !saved);
          if (icon) {
            icon.classList.toggle('text-red-500', saved);
            icon.classList.toggle('fill-red-500', saved);
            icon.classList.toggle('text-gray-900', !saved);
          }
        })
        .catch(function (err) {
          if (typeof console !== 'undefined' && console.warn) {
            console.warn('[explore favorites]', err && err.message ? err.message : err);
          }
        });
    });
  });
}

document.addEventListener('DOMContentLoaded', function () {
  var markers = window.__EXPLORE_MARKERS__;
  if (Array.isArray(markers)) {
    window.initExploreMaps(markers);
  }
  exploreSetupFavorites();
  exploreRefreshLucide();
});
