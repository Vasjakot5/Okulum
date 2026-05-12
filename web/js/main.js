let scale = 1.2;
let translateX = 0;
let translateY = 0;
let isDragging = false;
let startX, startY;
let isBurgerOpen = false;
let currentCountryId = window.currentCountryId || null;
let isLoading = false;
let popularHumansData = window.popularHumansData || [];
let currentIndex = 0;
let quoteInterval = null;

function adjustCityPoints() {
    const cityPoints = document.querySelectorAll('.city-point');
    const mapImage = document.getElementById('mapImage');
    const mapWrapper = document.querySelector('.map-wrapper');
    
    if (!mapImage || !mapImage.complete || cityPoints.length === 0) return;
    
    const imgWidth = mapImage.naturalWidth;
    const imgHeight = mapImage.naturalHeight;
    const wrapperWidth = mapWrapper.clientWidth;
    const wrapperHeight = mapWrapper.clientHeight;
    
    if (imgWidth === 0 || imgHeight === 0 || wrapperHeight === 0) return;
    
    const imgRatio = imgWidth / imgHeight;
    const wrapperRatio = wrapperWidth / wrapperHeight;
    
    let displayWidth, displayHeight, offsetX = 0, offsetY = 0;
    
    if (imgRatio > wrapperRatio) {
        displayWidth = wrapperWidth;
        displayHeight = wrapperWidth / imgRatio;
        offsetY = (wrapperHeight - displayHeight) / 2;
    } else {
        displayHeight = wrapperHeight;
        displayWidth = wrapperHeight * imgRatio;
        offsetX = (wrapperWidth - displayWidth) / 2;
    }
    
    const isMobile = window.innerWidth <= 768;
    const mobileYOffset = isMobile ? -5 : 0;
    const sizeMultiplier = 1.2 / scale;
    
    cityPoints.forEach(point => {
        const x = parseFloat(point.dataset.x) || 50;
        const y = parseFloat(point.dataset.y) || 50;
        
        let baseLeft = offsetX + (x * displayWidth / 100);
        let baseTop = offsetY + (y * displayHeight / 100);
        baseTop = baseTop + mobileYOffset;
        
        point.style.left = baseLeft + 'px';
        point.style.top = baseTop + 'px';
        
        let baseDiamondSize = isMobile ? 12 : 16;
        let baseFontSize = isMobile ? 10 : 12;
        
        const diamondSize = baseDiamondSize * sizeMultiplier;
        const fontSize = baseFontSize * sizeMultiplier * sizeMultiplier;
        
        const diamond = point.querySelector('.city-diamond');
        if (diamond) {
            diamond.style.width = diamondSize + 'px';
            diamond.style.height = diamondSize + 'px';
        }
        
        const name = point.querySelector('.city-name');
        if (name) {
            if (!point.classList.contains('hovered')) {
                name.style.opacity = '0.3';
            }
            const minFontSize = isMobile ? 6 : 8;
            name.style.fontSize = Math.max(fontSize, minFontSize) + 'px';
            const padding = Math.max(2, 4 * sizeMultiplier) + 'px ' + 
                           Math.max(4, 8 * sizeMultiplier) + 'px';
            name.style.padding = padding;
            name.style.border = scale > 1.8 ? '1px solid rgba(255,255,255,0.2)' : '1px solid rgba(255,255,255,0.05)';
        }
    });
}

function initMapEvents() {
    const mapImage = document.getElementById('mapImage');
    if (!mapImage) return;
    
    mapImage.removeEventListener('mousedown', startDrag);
    mapImage.removeEventListener('touchstart', startDrag);
    mapImage.removeEventListener('wheel', handleWheel);
    
    mapImage.addEventListener('mousedown', startDrag);
    mapImage.addEventListener('touchstart', startDrag, { passive: false });
    mapImage.addEventListener('wheel', handleWheel, { passive: false });
    mapImage.addEventListener('dblclick', function(e) {
        e.preventDefault();
        zoomIn();
    });
    
    document.removeEventListener('mousemove', drag);
    document.removeEventListener('mouseup', stopDrag);
    document.removeEventListener('touchmove', drag);
    document.removeEventListener('touchend', stopDrag);
    document.removeEventListener('touchcancel', stopDrag);
    
    document.addEventListener('mousemove', drag);
    document.addEventListener('mouseup', stopDrag);
    document.addEventListener('touchmove', drag, { passive: false });
    document.addEventListener('touchend', stopDrag);
    document.addEventListener('touchcancel', stopDrag);
    
    if (mapImage.complete) {
        adjustCityPoints();
    } else {
        mapImage.onload = function() {
            adjustCityPoints();
        };
    }
}

function handleWheel(e) {
    e.preventDefault();
    
    const wrapper = document.querySelector('.map-wrapper');
    const rect = wrapper.getBoundingClientRect();
    
    let mouseX, mouseY;
    
    if (e.touches) {
        mouseX = e.touches[0].clientX - rect.left;
        mouseY = e.touches[0].clientY - rect.top;
    } else {
        mouseX = e.clientX - rect.left;
        mouseY = e.clientY - rect.top;
    }
    
    let delta = e.deltaY > 0 ? -0.1 : 0.1;
    
    if (window.innerWidth <= 768) {
        delta = e.deltaY > 0 ? -0.05 : 0.05;
    }
    
    let newScale = Math.max(0.8, Math.min(2.4, scale + delta));
    
    if (newScale !== scale) {
        const oldScale = scale;
        scale = newScale;
        
        const centerX = rect.width / 2;
        const centerY = rect.height / 2;
        
        const offsetX = mouseX - centerX;
        const offsetY = mouseY - centerY;
        
        const scaleFactor = scale / oldScale;
        translateX = (translateX - offsetX) * scaleFactor + offsetX;
        translateY = (translateY - offsetY) * scaleFactor + offsetY;
        
        const maxX = (rect.width * (scale - 1)) / 2;
        const maxY = (rect.height * (scale - 1)) / 2;
        translateX = Math.max(Math.min(translateX, maxX), -maxX);
        translateY = Math.max(Math.min(translateY, maxY), -maxY);
        
        updateZoom();
    }
}

function startDrag(e) {
    if (scale <= 1) return;
    
    isDragging = true;
    let x, y;
    
    if (e.touches) {
        x = e.touches[0].clientX;
        y = e.touches[0].clientY;
    } else {
        x = e.clientX;
        y = e.clientY;
    }
    
    if (x === undefined) return;
    
    const wrapper = document.querySelector('.map-wrapper');
    const rect = wrapper.getBoundingClientRect();
    x = x - rect.left;
    y = y - rect.top;
    
    const centerX = rect.width / 2;
    const centerY = rect.height / 2;
    
    startX = x - centerX - translateX;
    startY = y - centerY - translateY;
    
    const mapImage = document.getElementById('mapImage');
    if (mapImage) mapImage.style.cursor = 'grabbing';
    e.preventDefault();
}

function drag(e) {
    if (!isDragging || scale <= 1) return;
    e.preventDefault();
    
    let x, y;
    
    if (e.touches) {
        x = e.touches[0].clientX;
        y = e.touches[0].clientY;
    } else {
        x = e.clientX;
        y = e.clientY;
    }
    
    if (x === undefined) return;
    
    const wrapper = document.querySelector('.map-wrapper');
    const rect = wrapper.getBoundingClientRect();
    x = x - rect.left;
    y = y - rect.top;
    
    const centerX = rect.width / 2;
    const centerY = rect.height / 2;
    
    translateX = x - centerX - startX;
    translateY = y - centerY - startY;
    
    const maxX = (rect.width * (scale - 1)) / 2;
    const maxY = (rect.height * (scale - 1)) / 2;
    translateX = Math.max(Math.min(translateX, maxX), -maxX);
    translateY = Math.max(Math.min(translateY, maxY), -maxY);
    
    updateZoom();
}

function stopDrag() {
    isDragging = false;
    const mapImage = document.getElementById('mapImage');
    if (mapImage) {
        mapImage.style.cursor = scale > 1 ? 'grab' : 'default';
    }
}

function zoomIn() {
    if (scale < 2.4) {
        const wrapper = document.querySelector('.map-wrapper');
        const rect = wrapper.getBoundingClientRect();
        
        const oldScale = scale;
        const step = window.innerWidth <= 768 ? 0.1 : 0.2;
        scale = Math.min(scale + step, 2.4);
        
        const scaleFactor = scale / oldScale;
        translateX = translateX * scaleFactor;
        translateY = translateY * scaleFactor;
        
        const maxX = (rect.width * (scale - 1)) / 2;
        const maxY = (rect.height * (scale - 1)) / 2;
        translateX = Math.max(Math.min(translateX, maxX), -maxX);
        translateY = Math.max(Math.min(translateY, maxY), -maxY);
        
        updateZoom();
    }
}

function zoomOut() {
    if (scale > 0.8) {
        const wrapper = document.querySelector('.map-wrapper');
        const rect = wrapper.getBoundingClientRect();
        
        const oldScale = scale;
        const step = window.innerWidth <= 768 ? 0.1 : 0.2;
        scale = Math.max(scale - step, 0.8);
        
        const scaleFactor = scale / oldScale;
        translateX = translateX * scaleFactor;
        translateY = translateY * scaleFactor;
        
        const maxX = (rect.width * (scale - 1)) / 2;
        const maxY = (rect.height * (scale - 1)) / 2;
        translateX = Math.max(Math.min(translateX, maxX), -maxX);
        translateY = Math.max(Math.min(translateY, maxY), -maxY);
        
        updateZoom();
    }
}

function resetZoom() {
    scale = 1.2;
    translateX = 0;
    translateY = 0;
    updateZoom();
}

function updateZoom() {
    const mapContainer = document.getElementById('mapContainer');
    if (mapContainer) {
        mapContainer.style.transform = `scale(${scale}) translate(${translateX}px, ${translateY}px)`;
        
        const percent = Math.round((scale / 1.2) * 100);
        const zoomLevel = document.getElementById('zoomLevel');
        if (zoomLevel) zoomLevel.textContent = percent + '%';
        
        adjustCityPoints();
    }
}

function selectCountryAjax(countryId) {
    if (currentCountryId === countryId || isLoading) return;
    
    isLoading = true;
    
    var mapContent = document.getElementById('countryMapContent');
    if (mapContent) {
        mapContent.innerHTML = '<div class="map-loading"><div class="spinner"></div><p>Загрузка карты...</p></div>';
    }
    
    var xhr = new XMLHttpRequest();
    var url = window.getCountryDataUrl + '?country_id=' + countryId;
    
    xhr.open('GET', url, true);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.timeout = 10000;
    
    xhr.onload = function() {
        if (xhr.status === 200) {
            try {
                var response = JSON.parse(xhr.responseText);
                
                if (response.success) {
                    currentCountryId = countryId;
                    
                    var newUrl = window.location.pathname + '?country_id=' + countryId;
                    window.history.pushState({ country_id: countryId }, '', newUrl);
                    
                    var headerContent = document.getElementById('countryHeaderContent');
                    if (headerContent) {
                        headerContent.innerHTML = response.headerHtml;
                    }
                    
                    var mapContent = document.getElementById('countryMapContent');
                    if (mapContent) {
                        mapContent.innerHTML = response.mapHtml;
                    }
                    
                    setTimeout(function() {
                        resetZoom();
                        initMapEvents();
                        initCityPointsEvents();
                        adjustCityPoints();
                        initEditModeButton();
                        isLoading = false;
                    }, 100);
                } else {
                    showError(response.message || 'Ошибка загрузки данных');
                    isLoading = false;
                }
            } catch (e) {
                console.error('Ошибка парсинга JSON:', e);
                showError('Ошибка обработки ответа сервера');
                isLoading = false;
            }
        } else {
            showError('Ошибка сети (статус: ' + xhr.status + ')');
            isLoading = false;
        }
    };
    
    xhr.onerror = function() {
        isLoading = false;
        showError('Ошибка сети. Пожалуйста, проверьте соединение.');
    };
    
    xhr.ontimeout = function() {
        isLoading = false;
        showError('Превышено время ожидания ответа от сервера');
    };
    
    xhr.send();
}

function showError(message) {
    var mapContent = document.getElementById('countryMapContent');
    if (mapContent) {
        mapContent.innerHTML = '<div class="error-message"><i class="fas fa-exclamation-triangle"></i><p>' + message + '</p><button onclick="location.reload()" style="margin-top: 20px; padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer;">Обновить страницу</button></div>';
    }
}

function initCityPointsEvents() {
    document.querySelectorAll('.city-point').forEach(point => {
        const diamond = point.querySelector('.city-diamond');
        const name = point.querySelector('.city-name');
        const id = point.dataset.cityId;
        const cityName = point.dataset.cityName;
        
        if (diamond) {
            diamond.addEventListener('mouseenter', function(e) {
                e.stopPropagation();
                point.classList.add('hovered');
                point.style.setProperty('z-index', '2000', 'important');
                name.style.setProperty('opacity', '1', 'important');
                name.style.setProperty('background-color', 'rgba(0, 0, 0, 0.98)', 'important');
                name.style.setProperty('border', '1px solid rgba(255, 255, 255, 0.3)', 'important');
                name.style.setProperty('box-shadow', '0 4px 8px rgba(0,0,0,0.5)', 'important');
                diamond.style.setProperty('transform', 'scale(1.3)', 'important');
                diamond.style.setProperty('z-index', '2001', 'important');
                diamond.style.setProperty('box-shadow', '0 4px 8px rgba(0,0,0,0.5)', 'important');
                
                document.querySelectorAll('.city-point').forEach(otherPoint => {
                    if (otherPoint !== point) {
                        const otherName = otherPoint.querySelector('.city-name');
                        if (otherName) {
                            otherName.style.setProperty('opacity', '0.15', 'important');
                        }
                    }
                });
            });
            
            diamond.addEventListener('mouseleave', function(e) {
                e.stopPropagation();
                point.classList.remove('hovered');
                point.style.setProperty('z-index', '10', 'important');
                name.style.setProperty('opacity', '0.3', 'important');
                name.style.setProperty('background-color', 'rgba(0, 0, 0, 0.95)', 'important');
                name.style.setProperty('border', scale > 1.8 ? '1px solid rgba(255,255,255,0.2)' : '1px solid rgba(255,255,255,0.05)', 'important');
                name.style.setProperty('box-shadow', '0 2px 4px rgba(0,0,0,0.2)', 'important');
                diamond.style.setProperty('transform', 'scale(1)', 'important');
                diamond.style.setProperty('z-index', '101', 'important');
                diamond.style.setProperty('box-shadow', '0 2px 4px rgba(0,0,0,0.3)', 'important');
                
                document.querySelectorAll('.city-point').forEach(otherPoint => {
                    const otherName = otherPoint.querySelector('.city-name');
                    if (otherName) {
                        otherName.style.setProperty('opacity', '0.3', 'important');
                    }
                });
            });
            
            diamond.addEventListener('click', function(e) {
                e.stopPropagation();
                if (id && id != '0') {
                    window.location.href = window.cityViewUrl + '?id=' + id;
                }
            });
            
            diamond.addEventListener('touchstart', function(e) {
                e.preventDefault();
                
                document.querySelectorAll('.city-point').forEach(p => {
                    p.classList.remove('hovered');
                    const n = p.querySelector('.city-name');
                    if (n) {
                        n.style.setProperty('opacity', '0.3', 'important');
                    }
                });
                
                point.classList.add('hovered');
                point.style.setProperty('z-index', '2000', 'important');
                name.style.setProperty('opacity', '1', 'important');
                name.style.setProperty('background-color', 'rgba(0, 0, 0, 0.98)', 'important');
                name.style.setProperty('border', '1px solid rgba(255, 255, 255, 0.3)', 'important');
                diamond.style.setProperty('transform', 'scale(1.3)', 'important');
                diamond.style.setProperty('z-index', '2001', 'important');
                
                document.querySelectorAll('.city-point').forEach(otherPoint => {
                    if (otherPoint !== point) {
                        const otherName = otherPoint.querySelector('.city-name');
                        if (otherName) {
                            otherName.style.setProperty('opacity', '0.15', 'important');
                        }
                    }
                });
            });
            
            diamond.addEventListener('touchend', function(e) {
                e.preventDefault();
                let point = this.closest('.city-point');
                let id = point.dataset.cityId;
                let cityName = point.dataset.cityName;
                
                if (id && id != '0') {
                    setTimeout(() => {
                        if (confirm('Перейти к городу ' + cityName + '?')) {
                            window.location.href = window.cityViewUrl + '?id=' + id;
                        }
                    }, 100);
                }
                
                setTimeout(() => {
                    point.classList.remove('hovered');
                    point.style.setProperty('z-index', '10', 'important');
                    name.style.setProperty('opacity', '0.3', 'important');
                    name.style.setProperty('background-color', 'rgba(0, 0, 0, 0.95)', 'important');
                    diamond.style.setProperty('transform', 'scale(1)', 'important');
                    diamond.style.setProperty('z-index', '101', 'important');
                    
                    document.querySelectorAll('.city-point').forEach(otherPoint => {
                        const otherName = otherPoint.querySelector('.city-name');
                        if (otherName) {
                            otherName.style.setProperty('opacity', '0.3', 'important');
                        }
                    });
                }, 300);
            });
        }
    });
}

function toggleBurgerMenu() {
    isBurgerOpen = !isBurgerOpen;
    let dropdown = document.getElementById('burgerDropdown');
    let header = document.querySelector('.burger-header');
    
    if (isBurgerOpen) {
        dropdown.classList.add('open');
        header.classList.add('active');
    } else {
        dropdown.classList.remove('open');
        header.classList.remove('active');
    }
}

function closeBurgerMenu() {
    isBurgerOpen = false;
    let dropdown = document.getElementById('burgerDropdown');
    let header = document.querySelector('.burger-header');
    
    if (dropdown) dropdown.classList.remove('open');
    if (header) header.classList.remove('active');
}

function updateQuote() {
    let availableHumans = popularHumansData.filter(function(human) {
        return human.quote && human.quote.trim() !== '';
    });
    
    if (availableHumans.length === 0) return;
    
    let newIndex;
    do {
        newIndex = Math.floor(Math.random() * availableHumans.length);
    } while (newIndex === currentIndex && availableHumans.length > 1);
    
    currentIndex = newIndex;
    const human = availableHumans[currentIndex];
    
    const quoteText = document.getElementById('quoteText');
    const quoteImage = document.getElementById('quoteImage');
    
    if (!quoteText || !quoteImage) return;
    
    quoteText.style.opacity = '0';
    quoteImage.style.opacity = '0';
    
    setTimeout(() => {
        quoteText.innerHTML = `
            <p class="quote-prefix">
                Знаменитое высказывание ${escapeHtml(human.type)} 
                ${escapeHtml(human.country_name)}:
            </p>
            <p class="quote-text">"${escapeHtml(human.quote)}"</p>
        `;
        
        if (human.img) {
            quoteImage.innerHTML = `
                <img src="${window.popularHumansImgsPath}/${human.img}" 
                    alt="${escapeHtml(human.name + ' ' + human.last_name)}"
                    class="human-quote-img">
                <b>${escapeHtml(human.name + ' ' + human.last_name)}</b>
                <p>${formatDate(human.date_born)}${human.date_death ? ' - ' + formatDate(human.date_death) : ' - Н.В.'}</p>
            `;
        }
        
        quoteText.style.opacity = '1';
        quoteImage.style.opacity = '1';
    }, 500);
}

function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/[&<>]/g, function(m) {
        if (m === '&') return '&amp;';
        if (m === '<') return '&lt;';
        if (m === '>') return '&gt;';
        return m;
    });
}

function formatDate(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('ru-RU');
}

function initCatalog() {
    const searchInput = document.getElementById('searchInput');
    const catalogResults = document.getElementById('catalogResults');
    const alphabetFilter = document.getElementById('alphabetFilter');
    const searchLoader = document.getElementById('searchLoader');
    const headerTitle = document.querySelector('.catalog-header h1');
    
    if (!searchInput || !catalogResults) return;
    
    let searchTimeout;
    let currentType = window.currentType || 'all';
    let currentCountry = window.currentCountryId || 'all';
    let currentLetter = window.currentLetter || 'all';
    let currentPage = 1;
    
    function updateHeaderTitle(type) {
        let icon = '';
        let title = '';
        
        switch(type) {
            case 'all':
                icon = '<i class="fas fa-book-open"></i>';
                title = 'Все материалы';
                break;
            case 'cities':
                icon = '<i class="fas fa-city"></i>';
                title = 'Города';
                break;
            case 'events':
                icon = '<i class="fas fa-calendar-alt"></i>';
                title = 'События';
                break;
            case 'openings':
                icon = '<i class="fas fa-compass"></i>';
                title = 'Открытия';
                break;
            case 'humans':
                icon = '<i class="fas fa-users"></i>';
                title = 'Знаменитые люди';
                break;
            case 'vehicles':
                icon = '<i class="fas fa-cogs"></i>';
                title = 'Техника';
                break;
            case 'monuments':
                icon = '<i class="fas fa-landmark"></i>';
                title = 'Памятники';
                break;
            case 'weapons':
                icon = '<i class="fas fa-shield-alt"></i>';
                title = 'Оружие';
                break;
            case 'clothes':
                icon = '<i class="fas fa-tshirt"></i>';
                title = 'Одежда';
                break;
            default:
                icon = '<i class="fas fa-book-open"></i>';
                title = 'Все материалы';
        }
        
        if (headerTitle) {
            headerTitle.innerHTML = `${icon} ${title}`;
        }
    }
    
    function performSearch(page = 1) {
        const searchQuery = searchInput.value.trim();
        const url = new URL(window.location.href);
        
        url.searchParams.set('type', currentType);
        url.searchParams.set('country_id', currentCountry);
        url.searchParams.set('page', page);
        
        if (currentLetter !== 'all' && currentLetter !== '') {
            url.searchParams.set('letter', currentLetter);
        } else {
            url.searchParams.delete('letter');
        }
        
        if (searchQuery) {
            url.searchParams.set('search', searchQuery);
            url.searchParams.delete('letter');
        } else {
            url.searchParams.delete('search');
        }
        
        window.history.pushState({}, '', url);
        
        if (searchLoader) searchLoader.style.display = 'block';
        if (catalogResults) catalogResults.style.opacity = '0.5';
        
        fetch(url.toString(), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            if (catalogResults) {
                catalogResults.innerHTML = html;
                catalogResults.style.opacity = '1';
            }
            if (searchLoader) searchLoader.style.display = 'none';
            
            updateHeaderTitle(currentType);
            
            if (alphabetFilter) {
                if (searchQuery) {
                    alphabetFilter.style.display = 'none';
                } else {
                    alphabetFilter.style.display = 'flex';
                }
            }
            
            updateActiveStates();
            bindPaginationEvents();
        })
        .catch(error => {
            console.error('Ошибка:', error);
            if (searchLoader) searchLoader.style.display = 'none';
            if (catalogResults) catalogResults.style.opacity = '1';
        });
    }
    
    function updateActiveStates() {
        document.querySelectorAll('.country-btn').forEach(btn => {
            const country = btn.getAttribute('data-country');
            if (country === currentCountry) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });
        
        document.querySelectorAll('.type-btn').forEach(btn => {
            const type = btn.getAttribute('data-type');
            if (type === currentType) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });
        
        document.querySelectorAll('.letter-btn').forEach(btn => {
            const letter = btn.getAttribute('data-letter');
            if (letter === currentLetter) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });
    }
    
    function bindPaginationEvents() {
        document.querySelectorAll('.pagination-btn:not(.disabled)').forEach(btn => {
            btn.removeEventListener('click', paginationHandler);
            btn.addEventListener('click', paginationHandler);
        });
    }
    
    function paginationHandler(e) {
        e.preventDefault();
        const page = parseInt(this.getAttribute('data-page'));
        if (page && !isNaN(page)) {
            currentPage = page;
            performSearch(page);
        }
    }
    
    document.querySelectorAll('.country-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            currentCountry = this.getAttribute('data-country');
            currentLetter = 'all';
            currentPage = 1;
            performSearch(1);
        });
    });
    
    document.querySelectorAll('.type-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            currentType = this.getAttribute('data-type');
            currentLetter = 'all';
            currentPage = 1;
            performSearch(1);
        });
    });
    
    document.querySelectorAll('.letter-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            currentLetter = this.getAttribute('data-letter');
            currentPage = 1;
            performSearch(1);
        });
    });
    
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            currentPage = 1;
            performSearch(1);
        }, 500);
    });
    
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            clearTimeout(searchTimeout);
            currentPage = 1;
            performSearch(1);
        }
    });
    
    window.addEventListener('popstate', function() {
        const urlParams = new URLSearchParams(window.location.search);
        currentType = urlParams.get('type') || 'all';
        currentCountry = urlParams.get('country_id') || 'all';
        currentLetter = urlParams.get('letter') || 'all';
        currentPage = parseInt(urlParams.get('page')) || 1;
        
        if (searchInput) {
            searchInput.value = urlParams.get('search') || '';
        }
        
        updateHeaderTitle(currentType);
        performSearch(currentPage);
    });
    
    bindPaginationEvents();
}

function fixMapHeight() {
    const mapWrapper = document.querySelector('.map-wrapper');
    if (mapWrapper && mapWrapper.clientHeight === 0) {
        mapWrapper.style.height = '600px';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    if (document.querySelector('.map-wrapper')) {
        initMapEvents();
        initCityPointsEvents();
        initEditModeButton();
        adjustCityPoints();
        fixMapHeight();
    }

    if (popularHumansData.length > 0 && document.querySelector('.hello-quote-section')) {
        currentIndex = Math.floor(Math.random() * popularHumansData.length);
        updateQuote();
        
        if (quoteInterval) clearInterval(quoteInterval);
        quoteInterval = setInterval(updateQuote, 5000);
    }
    
    if (document.querySelector('.catalog-page')) {
        initCatalog();
    }
    
    window.addEventListener('resize', function() {
        if (document.querySelector('.map-wrapper')) {
            adjustCityPoints();
        }
    });
    
    window.addEventListener('orientationchange', function() {
        if (document.querySelector('.map-wrapper')) {
            setTimeout(adjustCityPoints, 100);
        }
    });
    
    document.addEventListener('click', function(e) {
        var container = document.querySelector('.burger-menu-container');
        if (container && !container.contains(e.target)) {
            closeBurgerMenu();
        }
    });
    
    window.addEventListener('popstate', function(event) {
        if (event.state && event.state.country_id && document.querySelector('.map-wrapper')) {
            selectCountryAjax(event.state.country_id);
        } else if (document.querySelector('.map-wrapper')) {
            location.reload();
        }
    });
    
    if (popularHumansData.length > 0 && document.querySelector('.hello-quote-section')) {
        currentIndex = Math.floor(Math.random() * popularHumansData.length);
        if (quoteInterval) clearInterval(quoteInterval);
        quoteInterval = setInterval(updateQuote, 5000);
    }
});

document.addEventListener('wheel', function(e) {
    if (e.target.closest('.map-wrapper')) {
        e.preventDefault();
    }
}, { passive: false });

document.addEventListener('touchmove', function(e) {
    if (e.target.closest('.map-wrapper')) {
        e.preventDefault();
    }
}, { passive: false });

$(document).ready(function() {
    console.log('Comments initialized');
    
    $('.ajax-comment-form').off('beforeSubmit').on('beforeSubmit', function(e) {
        e.preventDefault();
        var form = $(this);
        var btn = form.find('.submit-comment-btn');
        var original = btn.html();
        
        btn.html('<i class="fas fa-spinner fa-spin"></i> Публикация...').prop('disabled', true);
        
        $.ajax({
            url: addCommentUrl,
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(res) {
                if (res.success) location.reload();
                else alert(res.message);
                btn.html(original).prop('disabled', false);
            },
            error: function() {
                alert('Ошибка');
                btn.html(original).prop('disabled', false);
            }
        });
        return false;
    });
    
    $('.reply-trigger').off('click').on('click', function() {
        var id = $(this).data('id');
        $('#reply-form-' + id).slideToggle(200);
    });
    
    $('.submit-reply-btn').off('click').on('click', function() {
        var id = $(this).data('id');
        var content = $('#reply-content-' + id).val();
        var entityType = $('input[name="entity_type"]').val();
        var entityId = $('input[name="entity_id"]').val();
        var btn = $(this);
        var original = btn.html();
        
        if (!content.trim()) {
            alert('Введите текст');
            return;
        }
        
        btn.html('<i class="fas fa-spinner fa-spin"></i> Отправка...').prop('disabled', true);
        
        $.ajax({
            url: addCommentUrl,
            type: 'POST',
            data: {
                content: content,
                parent_id: id,
                entity_type: entityType,
                entity_id: entityId
            },
            dataType: 'json',
            success: function(res) {
                if (res.success) location.reload();
                else alert(res.message);
                btn.html(original).prop('disabled', false);
            },
            error: function() {
                alert('Ошибка');
                btn.html(original).prop('disabled', false);
            }
        });
    });
    
    $('.cancel-reply-btn').off('click').on('click', function() {
        var id = $(this).data('id');
        $('#reply-form-' + id).slideUp(200);
        $('#reply-content-' + id).val('');
    });
    
    $('.edit-comment-trigger').off('click').on('click', function() {
        var id = $(this).data('id');
        $('#comment-text-' + id).hide();
        $('#edit-form-' + id).show();
    });
    
    $('.save-edit-btn').off('click').on('click', function() {
        var id = $(this).data('id');
        var newContent = $('#edit-content-' + id).val();
        var btn = $(this);
        var original = btn.html();
        
        if (!newContent.trim()) {
            alert('Комментарий не может быть пустым');
            return;
        }
        
        btn.html('<i class="fas fa-spinner fa-spin"></i> Сохранение...').prop('disabled', true);
        
        $.ajax({
            url: editCommentUrl,
            type: 'POST',
            data: {
                id: id,
                content: newContent
            },
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    location.reload();
                } else {
                    alert(res.message);
                    btn.html(original).prop('disabled', false);
                }
            },
            error: function() {
                alert('Ошибка при редактировании');
                btn.html(original).prop('disabled', false);
            }
        });
    });
    
    $('.cancel-edit-btn').off('click').on('click', function() {
        var id = $(this).data('id');
        $('#edit-form-' + id).hide();
        $('#comment-text-' + id).show();
    });
    
    $('.delete-comment-trigger').off('click').on('click', function() {
        if (!confirm('Удалить комментарий?')) return;
        var id = $(this).data('id');
        var block = $('#comment-' + id);
        var btn = $(this);
        var original = btn.html();
        
        btn.html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);
        
        $.ajax({
            url: deleteCommentUrl + '?id=' + id,
            type: 'POST',
            success: function(res) {
                if (res.success) block.fadeOut(300, function() { $(this).remove(); });
                else alert(res.message);
                btn.html(original).prop('disabled', false);
            },
            error: function() { 
                alert('Ошибка');
                btn.html(original).prop('disabled', false);
            }
        });
    });
    
    $('.delete-reply-trigger').off('click').on('click', function() {
        if (!confirm('Удалить ответ?')) return;
        var id = $(this).data('id');
        var block = $('#reply-' + id);
        var btn = $(this);
        var original = btn.html();
        
        btn.html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);
        
        $.ajax({
            url: deleteCommentUrl + '?id=' + id,
            type: 'POST',
            success: function(res) {
                if (res.success) block.fadeOut(300, function() { $(this).remove(); });
                else alert(res.message);
                btn.html(original).prop('disabled', false);
            },
            error: function() { 
                alert('Ошибка');
                btn.html(original).prop('disabled', false);
            }
        });
    });
});

(function() {
    var speakBtns = document.querySelectorAll('.speak-btn');
    
    if (speakBtns.length === 0) return;
    
    function stopSpeaking(btn) {
        window.speechSynthesis.cancel();
        setTimeout(function() {
            window.speechSynthesis.cancel();
        }, 10);
        btn.innerHTML = '<i class="fas fa-volume-up"></i> Озвучить';
        btn.classList.remove('playing');
    }
    
    speakBtns.forEach(function(btn) {
        var text = btn.getAttribute('data-text');
        if (!text) return;
        
        var isPlaying = false;
        
        btn.onclick = function() {
            if (isPlaying) {
                stopSpeaking(btn);
                isPlaying = false;
                return;
            }
            
            if (!window.speechSynthesis) {
                alert('Ваш браузер не поддерживает озвучку');
                return;
            }
            
            window.speechSynthesis.cancel();
            
            var utterance = new SpeechSynthesisUtterance(text);
            utterance.lang = 'ru-RU';
            utterance.rate = 0.9;
            
            utterance.onstart = function() {
                isPlaying = true;
                btn.innerHTML = '<i class="fas fa-stop"></i> Остановить';
                btn.classList.add('playing');
            };
            
            utterance.onend = function() {
                isPlaying = false;
                btn.innerHTML = '<i class="fas fa-volume-up"></i> Озвучить';
                btn.classList.remove('playing');
            };
            
            utterance.onerror = function() {
                isPlaying = false;
                btn.innerHTML = '<i class="fas fa-volume-up"></i> Озвучить';
                btn.classList.remove('playing');
            };
            
            window.speechSynthesis.speak(utterance);
        };
    });
})();

document.querySelectorAll('.type-tab').forEach(tab => {
    tab.addEventListener('click', function(e) {
        e.preventDefault();
        
        document.querySelectorAll('.type-tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('[class^="form-"]').forEach(f => f.classList.remove('active'));
        
        this.classList.add('active');
        const type = this.dataset.type;
        document.getElementById('form-' + type).classList.add('active');
        
        const url = new URL(window.location.href);
        url.searchParams.set('type', type);
        window.history.pushState({}, '', url);
    });
});

const urlParams = new URLSearchParams(window.location.search);
const typeParam = urlParams.get('type');
if (typeParam) {
    const tab = document.querySelector(`.type-tab[data-type="${typeParam}"]`);
    if (tab) {
        tab.click();
    }
}

var discussionId = 0;
var currentUserId = 0;
var lastMessageId = 0;
var isScrolling = false;

var sendUrl = '';
var deleteUrl = '';
var getMessagesUrl = '';

function initChat(config) {
    discussionId = config.discussionId;
    currentUserId = config.currentUserId;
    sendUrl = config.sendUrl;
    deleteUrl = config.deleteUrl;
    getMessagesUrl = config.getMessagesUrl;
    
    setupScrollTracking();
    loadMessages();
    
    setInterval(function() {
        loadMessages();
    }, 3000);
}

function loadMessages() {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', getMessagesUrl);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.onload = function() {
        if (xhr.status === 200) {
            var res = JSON.parse(xhr.responseText);
            if (res.success) {
                renderMessages(res.messages);
                var countEl = document.getElementById('messages-count');
                if (countEl) {
                    countEl.innerText = res.messages.length + ' сообщений';
                }
            }
        }
    };
    xhr.send();
}

function renderMessages(messages) {
    var container = document.getElementById('chat-messages');
    if (!container) return;
    
    var chatContainer = document.getElementById('chat-container');
    var wasAtBottom = chatContainer && (chatContainer.scrollHeight - chatContainer.scrollTop - chatContainer.clientHeight) < 100;
    
    if (messages.length === 0) {
        container.innerHTML = '<div class="empty-state"><i class="fas fa-comments"></i><h4>Пока нет сообщений</h4><p>Будьте первым, кто напишет сообщение</p></div>';
        return;
    }
    
    var html = '';
    for (var i = 0; i < messages.length; i++) {
        var msg = messages[i];
        var isOwner = (msg.user_id == currentUserId);
        
        if (isOwner) {
            html += `
                <div class="message-item message-item-owner">
                    <div class="message-bubble message-bubble-owner">
                        <div class="message-author"><b>${escapeHtml(msg.user_name)}</b></div>
                        <div class="message-text message-text-owner">${msg.content}</div>
                        <div class="message-footer">
                            <span class="message-time">${msg.created_at}</span>
                            <button class="delete-msg-btn back-btn status-badge" data-id="${msg.id}">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
        } else {
            html += `
                <div class="message-item message-item-other">
                    <div class="message-bubble message-bubble-other">
                        <div class="message-author"><b>${escapeHtml(msg.user_name)}</b></div>
                        <div class="message-text message-text-other">${msg.content}</div>
                        <div class="message-footer">
                            <span class="message-time">${msg.created_at}</span>
                        </div>
                    </div>
                </div>
            `;
        }
        
        if (msg.id > lastMessageId) lastMessageId = msg.id;
    }
    
    container.innerHTML = html;
    
    if (wasAtBottom && !isScrolling && chatContainer) {
        chatContainer.scrollTop = chatContainer.scrollHeight;
    }
}

function sendMessage() {
    var input = document.getElementById('message-input');
    var content = input.value.trim();
    
    if (!content) return;
    
    var btn = document.getElementById('send-btn');
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Отправка...';
    btn.disabled = true;
    
    var xhr = new XMLHttpRequest();
    xhr.open('POST', sendUrl);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (xhr.status === 200) {
            var res = JSON.parse(xhr.responseText);
            if (res.success) {
                input.value = '';
                loadMessages();
            } else {
                alert(res.message);
            }
        } else {
            alert('Ошибка');
        }
        btn.innerHTML = '<i class="fas fa-paper-plane"></i> Отправить';
        btn.disabled = false;
    };
    xhr.send('content=' + encodeURIComponent(content) + '&discussion_id=' + discussionId);
}

$(document).on('click', '.delete-msg-btn', function(e) {
    e.stopPropagation();
    var id = $(this).data('id');
    
    $.ajax({
        url: deleteUrl + '?id=' + id,
        type: 'POST',
        success: function(res) {
            if (res.success) {
                loadMessages();
            } else {
                alert(res.message);
            }
        }
    });
});

function setupScrollTracking() {
    var container = document.getElementById('chat-container');
    if (!container) return;
    
    container.addEventListener('scroll', function() {
        isScrolling = true;
        clearTimeout(window.scrollTimeout);
        window.scrollTimeout = setTimeout(function() {
            isScrolling = false;
        }, 1000);
    });
}

function escapeHtml(text) {
    if (!text) return '';
    var div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

document.addEventListener('DOMContentLoaded', function() {
    var sendBtn = document.getElementById('send-btn');
    var messageInput = document.getElementById('message-input');
    
    if (sendBtn) {
        sendBtn.onclick = sendMessage;
    }
    if (messageInput) {
        messageInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });
    }
});

var discussionId = null;
var currentUserId = null;
var lastMessageId = 0;
var isScrolling = false;
var openReplyForms = {};

var sendUrl = '/discussion/send';
var editUrl = '/discussion/edit-message';
var deleteUrl = '/discussion/delete-message';
var getMessagesUrl = '/discussion/get-messages';
var deleteDiscussionUrl = '/discussion/delete-discussion';

function initDiscussion(id, userId, urls) {
    discussionId = id;
    currentUserId = userId;
    
    if (urls) {
        if (urls.sendUrl) sendUrl = urls.sendUrl;
        if (urls.editUrl) editUrl = urls.editUrl;
        if (urls.deleteUrl) deleteUrl = urls.deleteUrl;
        if (urls.getMessagesUrl) getMessagesUrl = urls.getMessagesUrl;
        if (urls.deleteDiscussionUrl) deleteDiscussionUrl = urls.deleteDiscussionUrl;
    }
    
    setupScrollTracking();
    loadMessages();
    
    setInterval(function() {
        loadMessages();
    }, 3000);
}

function loadMessages() {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', getMessagesUrl + '?id=' + discussionId);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.onload = function() {
        if (xhr.status === 200) {
            var res = JSON.parse(xhr.responseText);
            if (res.success) {
                renderMessages(res.messages);
                var countEl = document.getElementById('messages-count');
                if (countEl) countEl.innerText = res.messages.length + ' сообщений';
            }
        }
    };
    xhr.send();
}

function renderMessages(messages) {
    var container = document.getElementById('chat-messages');
    if (!container) return;
    
    var chatContainer = document.getElementById('chat-container');
    var wasAtBottom = chatContainer && (chatContainer.scrollHeight - chatContainer.scrollTop - chatContainer.clientHeight) < 100;
    
    if (messages.length === 0) {
        container.innerHTML = '<div class="empty-state"><i class="fas fa-comments"></i><h4>Пока нет сообщений</h4><p>Будьте первым, кто напишет сообщение</p></div>';
        return;
    }
    
    var html = '';
    for (var i = 0; i < messages.length; i++) {
        var msg = messages[i];
        var isOwner = (msg.user_id == currentUserId);
        
        if (isOwner) {
            html += renderOwnerMessage(msg);
        } else {
            html += renderOtherMessage(msg);
        }
        
        if (msg.id > lastMessageId) lastMessageId = msg.id;
    }
    
    container.innerHTML = html;
    
    if (wasAtBottom && !isScrolling && chatContainer) {
        chatContainer.scrollTop = chatContainer.scrollHeight;
    }
}

function renderOwnerMessage(msg) {
    return `
        <div class="message-item message-item-owner" id="msg-${msg.id}">
            <div class="message-bubble message-bubble-owner">
                <div class="message-author"><b>${escapeHtml(msg.user_name)}</b></div>
                <div class="message-text message-text-owner" id="comment-text-${msg.id}">${msg.content}</div>
                <div class="edit-form" id="edit-form-${msg.id}" style="display: none;">
                    <textarea id="edit-content-${msg.id}" class="edit-textarea" rows="2">${stripHtml(msg.content)}</textarea>
                    <div class="edit-actions">
                        <button class="btn-action save-edit-btn" data-id="${msg.id}">Сохранить</button>
                        <button class="btn-action cancel-edit-btn" data-id="${msg.id}">Отмена</button>
                    </div>
                </div>
                <div class="message-footer">
                    <span class="message-time">${msg.created_at}</span>
                    <button class="delete-msg-btn status-badge back-btn bad" data-id="${msg.id}">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
}

function renderOtherMessage(msg) {
    return `
        <div class="message-item message-item-other" id="msg-${msg.id}">
            <div class="message-bubble message-bubble-other">
                <div class="message-author"><b>${escapeHtml(msg.user_name)}</b></div>
                <div class="message-text message-text-other">${msg.content}</div>
                <div class="message-footer">
                    <span class="message-time">${msg.created_at}</span>
                </div>
            </div>
        </div>
    `;
}

function sendMessage() {
    var input = document.getElementById('message-input');
    if (!input) return;
    
    var content = input.value.trim();
    if (!content) return;
    
    var btn = document.getElementById('send-btn');
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Отправка...';
    btn.disabled = true;
    
    var xhr = new XMLHttpRequest();
    xhr.open('POST', sendUrl);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (xhr.status === 200) {
            var res = JSON.parse(xhr.responseText);
            if (res.success) {
                input.value = '';
                loadMessages();
            } else {
                alert(res.message);
            }
        } else {
            alert('Ошибка');
        }
        btn.innerHTML = '<i class="fas fa-paper-plane"></i> Отправить';
        btn.disabled = false;
    };
    xhr.send('content=' + encodeURIComponent(content) + '&discussion_id=' + discussionId);
}

function editMessage(id, newContent) {
    var xhr = new XMLHttpRequest();
    xhr.open('POST', editUrl);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (xhr.status === 200) {
            var res = JSON.parse(xhr.responseText);
            if (res.success) {
                document.getElementById('comment-text-' + id).innerHTML = res.content;
                document.getElementById('edit-form-' + id).style.display = 'none';
                document.getElementById('comment-text-' + id).style.display = 'block';
            } else {
                alert(res.message);
            }
        } else {
            alert('Ошибка');
        }
    };
    xhr.send('id=' + id + '&content=' + encodeURIComponent(newContent));
}

function deleteMessage(id) {
    var xhr = new XMLHttpRequest();
    xhr.open('POST', deleteUrl + '?id=' + id);
    xhr.onload = function() {
        if (xhr.status === 200) {
            var res = JSON.parse(xhr.responseText);
            if (res.success) {
                loadMessages();
            } else {
                alert(res.message);
            }
        } else {
            alert('Ошибка');
        }
    };
    xhr.send();
}

function deleteDiscussion(id, title, elementId) {
    if (confirm('Удалить обсуждение "' + title + '"? Все сообщения в нем будут также удалены.')) {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', deleteDiscussionUrl);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            if (xhr.status === 200) {
                var res = JSON.parse(xhr.responseText);
                if (res.success) {
                    var el = document.getElementById(elementId);
                    if (el) {
                        el.style.transition = 'opacity 0.3s';
                        el.style.opacity = '0';
                        setTimeout(function() {
                            el.remove();
                            if (document.querySelectorAll('.discussion-item').length === 0) {
                                location.reload();
                            }
                        }, 300);
                    }
                } else {
                    alert(res.message);
                }
            } else {
                alert('Ошибка при удалении');
            }
        };
        xhr.send('id=' + id);
    }
}

function setupScrollTracking() {
    var container = document.getElementById('chat-container');
    if (!container) return;
    
    container.addEventListener('scroll', function() {
        isScrolling = true;
        clearTimeout(window.scrollTimeout);
        window.scrollTimeout = setTimeout(function() {
            isScrolling = false;
        }, 1000);
    });
}

function escapeHtml(text) {
    if (!text) return '';
    var div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function stripHtml(html) {
    if (!html) return '';
    var div = document.createElement('div');
    div.innerHTML = html;
    return div.textContent || div.innerText || '';
}

function bindEvents() {
    var sendBtn = document.getElementById('send-btn');
    if (sendBtn) {
        sendBtn.onclick = sendMessage;
    }
    
    var messageInput = document.getElementById('message-input');
    if (messageInput) {
        messageInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });
    }
    
    document.addEventListener('click', function(e) {
        var target = e.target;
        
        if (target.classList && target.classList.contains('edit-msg-btn')) {
            var id = target.getAttribute('data-id');
            var textDiv = document.getElementById('comment-text-' + id);
            var editForm = document.getElementById('edit-form-' + id);
            if (textDiv && editForm) {
                textDiv.style.display = 'none';
                editForm.style.display = 'block';
                var textarea = document.getElementById('edit-content-' + id);
                if (textarea) textarea.focus();
            }
        }
        
        if (target.classList && target.classList.contains('save-edit-btn')) {
            var id = target.getAttribute('data-id');
            var newContent = document.getElementById('edit-content-' + id).value.trim();
            if (newContent) {
                editMessage(id, newContent);
            } else {
                alert('Введите текст');
            }
        }
        
        if (target.classList && target.classList.contains('cancel-edit-btn')) {
            var id = target.getAttribute('data-id');
            document.getElementById('edit-form-' + id).style.display = 'none';
            document.getElementById('comment-text-' + id).style.display = 'block';
        }
        
        if (target.classList && target.classList.contains('delete-msg-btn')) {
            var id = target.getAttribute('data-id');
            if (confirm('Удалить сообщение?')) {
                deleteMessage(id);
            }
        }
    });
    
    document.addEventListener('click', function(e) {
        var target = e.target;
        var btn = target.closest('.btn-delete');
        if (btn && btn.classList && btn.classList.contains('btn-delete')) {
            e.stopPropagation();
            var id = btn.getAttribute('data-id');
            var title = btn.getAttribute('data-title');
            var elementId = 'discussion-' + id;
            deleteDiscussion(id, title, elementId);
        }
    });
}

(function() {
    'use strict';
    
    document.addEventListener('DOMContentLoaded', function() {
        var editBtn = document.getElementById('edit-answer-btn');
        var cancelBtn = document.getElementById('cancel-edit-btn');
        var answerDisplay = document.getElementById('answer-display');
        var answerEditForm = document.getElementById('answer-edit-form');
        
        if (editBtn) {
            editBtn.addEventListener('click', function() {
                if (answerDisplay) answerDisplay.style.display = 'none';
                if (answerEditForm) answerEditForm.style.display = 'block';
            });
        }
        
        if (cancelBtn) {
            cancelBtn.addEventListener('click', function() {
                if (answerDisplay) answerDisplay.style.display = 'block';
                if (answerEditForm) answerEditForm.style.display = 'none';
            });
        }
    });
})();

function initEditModeButton() {
    var editBtn = document.getElementById('editCoordsModeBtn');
    if (!editBtn) {
        console.log('Кнопка редактирования не найдена');
        return;
    }
    
    var newBtn = editBtn.cloneNode(true);
    editBtn.parentNode.replaceChild(newBtn, editBtn);
    
    newBtn.addEventListener('click', function() {
        window.isEditMode = !window.isEditMode;
        if (window.isEditMode) {
            newBtn.classList.add('active');
            newBtn.style.background = 'rgba(255, 0, 0, 0.7)';
            document.body.style.cursor = 'move';
            alert('Режим редактирования включен. Перетащите город мышкой!');
        } else {
            newBtn.classList.remove('active');
            newBtn.style.background = '';
            document.body.style.cursor = 'default';
            window.draggedCity = null;
        }
    });
    
    console.log('Кнопка редактирования инициализирована');
}

(function() {
    if (!window.isAdmin) return;
    
    window.isEditMode = false;
    window.draggedCity = null;
    
    var startMouseX = 0, startMouseY = 0;
    var startCityX = 0, startCityY = 0;
    var isDragging = false;
    
    function getPercentFromPixel(clientX, clientY) {
        var img = document.getElementById('mapImage');
        var mapContainer = document.querySelector('.map-container');
        if (!img || !mapContainer) return { x: 50, y: 50 };
        
        var imgRect = img.getBoundingClientRect();
        
        if (clientX < imgRect.left || clientX > imgRect.right || 
            clientY < imgRect.top || clientY > imgRect.bottom) {
            return null;
        }
        
        var percentX = (clientX - imgRect.left) / imgRect.width * 100;
        var percentY = (clientY - imgRect.top) / imgRect.height * 100;
        
        return {
            x: Math.min(100, Math.max(0, percentX)),
            y: Math.min(100, Math.max(0, percentY))
        };
    }
    
    function updateCityPositionOnMap(city, x, y) {
        var img = document.getElementById('mapImage');
        var mapContainer = document.querySelector('.map-container');
        if (!img || !mapContainer) return;
        
        var imgRect = img.getBoundingClientRect();
        var containerRect = mapContainer.getBoundingClientRect();
        
        var pixelX = imgRect.left - containerRect.left + (x / 100) * imgRect.width;
        var pixelY = imgRect.top - containerRect.top + (y / 100) * imgRect.height;
        
        city.style.left = pixelX + 'px';
        city.style.top = pixelY + 'px';
        city.setAttribute('data-x', x);
        city.setAttribute('data-y', y);
    }
    
    document.addEventListener('mousedown', function(e) {
        if (!window.isEditMode) return;
        
        var city = e.target.closest('.city-point');
        if (!city) return;
        
        e.preventDefault();
        e.stopPropagation();
        
        window.draggedCity = city;
        isDragging = false;
        startMouseX = e.clientX;
        startMouseY = e.clientY;
        
        startCityX = parseFloat(window.draggedCity.style.left) || 0;
        startCityY = parseFloat(window.draggedCity.style.top) || 0;
        
        window.draggedCity.style.pointerEvents = 'none';
        
        var diamond = window.draggedCity.querySelector('.city-diamond');
        if (diamond) {
            diamond.style.transform = 'scale(1.3)';
            diamond.style.boxShadow = '0 0 0 2px white, 0 0 0 4px #ff6b6b';
            diamond.style.cursor = 'grabbing';
        }
        var nameSpan = window.draggedCity.querySelector('.city-name');
        if (nameSpan) nameSpan.style.opacity = '1';
        
        window.draggedCity.style.zIndex = '10000';
    });
    
    document.addEventListener('mousemove', function(e) {
        if (!window.isEditMode || !window.draggedCity) return;
        
        e.preventDefault();
        
        var deltaX = e.clientX - startMouseX;
        var deltaY = e.clientY - startMouseY;
        
        if (Math.abs(deltaX) > 3 || Math.abs(deltaY) > 3) {
            isDragging = true;
        }
        
        var newLeft = startCityX + deltaX;
        var newTop = startCityY + deltaY;
        
        window.draggedCity.style.left = newLeft + 'px';
        window.draggedCity.style.top = newTop + 'px';
    });
    
    document.addEventListener('mouseup', function(e) {
        if (!window.isEditMode || !window.draggedCity) return;
        
        var cityId = window.draggedCity.getAttribute('data-city-id');
        if (!cityId || cityId === '0') {
            window.draggedCity.style.pointerEvents = '';
            resetDrag();
            return;
        }
        
        if (!isDragging) {
            window.draggedCity.style.pointerEvents = '';
            resetDrag();
            return;
        }
        
        var cityRect = window.draggedCity.getBoundingClientRect();
        var centerX = cityRect.left + cityRect.width / 2;
        var centerY = cityRect.top + cityRect.height / 2;
        
        var percent = getPercentFromPixel(centerX, centerY);
        
        if (!percent) {
            resetDrag();
            return;
        }
        
        var csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        var x = Math.round(percent.x);
        var y = Math.round(percent.y);
        
        fetch(window.updateCityAllCountriesUrl, {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-CSRF-Token': csrfToken
            },
            body: 'city_id=' + cityId + '&x=' + x + '&y=' + y
        })
        .then(function(response) { return response.json(); })
        .then(function(data) {
            if (data.success) {
                updateCityPositionOnMap(window.draggedCity, x, y);
                
                var diamond = window.draggedCity.querySelector('.city-diamond');
                if (diamond) {
                    diamond.style.backgroundColor = '#28a745';
                    setTimeout(function() {
                        if (diamond) {
                            diamond.style.backgroundColor = '';
                            diamond.style.transform = '';
                            diamond.style.boxShadow = '';
                        }
                    }, 500);
                }
                var nameSpan = window.draggedCity.querySelector('.city-name');
                if (nameSpan) nameSpan.style.opacity = '0.3';
                console.log('Сохранено: x=' + x + ', y=' + y);
            } else {
                console.error('Ошибка:', data.message);
                var oldX = window.draggedCity.getAttribute('data-x');
                var oldY = window.draggedCity.getAttribute('data-y');
                updateCityPositionOnMap(window.draggedCity, oldX, oldY);
            }
        })
        .catch(function(error) {
            console.error('Ошибка сети:', error);
            var oldX = window.draggedCity.getAttribute('data-x');
            var oldY = window.draggedCity.getAttribute('data-y');
            updateCityPositionOnMap(window.draggedCity, oldX, oldY);
        });
        
        window.draggedCity.style.pointerEvents = '';
        resetDrag();
    });
    
    function resetDrag() {
        if (window.draggedCity) {
            var diamond = window.draggedCity.querySelector('.city-diamond');
            if (diamond) {
                diamond.style.transform = '';
                diamond.style.boxShadow = '';
            }
            var nameSpan = window.draggedCity.querySelector('.city-name');
            if (nameSpan) nameSpan.style.opacity = '0.3';
            window.draggedCity.style.zIndex = '';
        }
        window.draggedCity = null;
        isDragging = false;
        startMouseX = 0;
        startMouseY = 0;
        startCityX = 0;
        startCityY = 0;
    }
})();

(function() {
    var originalAdjustCityPoints = window.adjustCityPoints;
    if (originalAdjustCityPoints) {
        window.adjustCityPoints = function() {
            originalAdjustCityPoints();
            if (window.isEditMode && window.draggedCity) {
                var x = window.draggedCity.getAttribute('data-x');
                var y = window.draggedCity.getAttribute('data-y');
                if (x && y) {
                    var img = document.getElementById('mapImage');
                    var mapContainer = document.querySelector('.map-container');
                    if (img && mapContainer) {
                        var imgRect = img.getBoundingClientRect();
                        var containerRect = mapContainer.getBoundingClientRect();
                        var pixelX = imgRect.left - containerRect.left + (parseFloat(x) / 100) * imgRect.width;
                        var pixelY = imgRect.top - containerRect.top + (parseFloat(y) / 100) * imgRect.height;
                        window.draggedCity.style.left = pixelX + 'px';
                        window.draggedCity.style.top = pixelY + 'px';
                    }
                }
            }
        };
    }
})();