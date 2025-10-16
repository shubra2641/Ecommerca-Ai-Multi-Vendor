// SIMPLIFIED CHECKOUT.JS FOR DEBUGGING
console.log('*** CHECKOUT.JS LOADED ***');
alert('Checkout.js loaded successfully!');

(function () {
    console.log('*** IIFE STARTED ***');

    document.addEventListener('DOMContentLoaded', function () {
        console.log('*** DOM READY ***');

        const countrySelect = document.getElementById('shipping-country');
        const govSelect = document.getElementById('shipping-governorate');
        const citySelect = document.getElementById('shipping-city');

        console.log('Elements found:', {
            country: !!countrySelect,
            governorate: !!govSelect,
            city: !!citySelect
        });

        if (countrySelect) {
            countrySelect.addEventListener('change', function () {
                console.log('Country changed to:', this.value);

                if (this.value) {
                    // Show governorate select
                    govSelect.style.display = 'block';
                    govSelect.innerHTML = '<option value="">Loading...</option>';

                    // Load governorates
                    fetch(` / api / locations / governorates ? country = ${this.value}`)
                        .then(response => {
                            console.log('API response status:', response.status);
                            return response.json();
                        })
                        .then(data => {
                            console.log('Governorates data:', data);

                            govSelect.innerHTML = '<option value="">Select Governorate</option>';
                            if (data.data && data.data.length > 0) {
                                data.data.forEach(gov => {
                                    govSelect.innerHTML += ` < option value = "${gov.id}" > ${gov.name} < / option > `;
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error loading governorates:', error);
                        });
                } else {
                    govSelect.style.display = 'none';
                    citySelect.style.display = 'none';
                }
            });
        }

        if (govSelect) {
            govSelect.addEventListener('change', function () {
                console.log('Governorate changed to:', this.value);

                if (this.value) {
                    // Show city select
                    citySelect.style.display = 'block';
                    citySelect.innerHTML = '<option value="">Loading...</option>';

                    // Load cities
                    fetch(` / api / locations / cities ? governorate = ${this.value}`)
                        .then(response => {
                            console.log('Cities API response status:', response.status);
                            return response.json();
                        })
                        .then(data => {
                            console.log('Cities data:', data);

                            citySelect.innerHTML = '<option value="">Select City</option>';
                            if (data.data && data.data.length > 0) {
                                data.data.forEach(city => {
                                    citySelect.innerHTML += ` < option value = "${city.id}" > ${city.name} < / option > `;
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error loading cities:', error);
                        });
                } else {
                    citySelect.style.display = 'none';
                }
            });
        }
    });
})();

