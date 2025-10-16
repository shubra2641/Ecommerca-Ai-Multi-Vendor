// Activity Center Vue app (extracted from Blade inline script)
// Progressive enhancement: falls back to static server-rendered list if JS disabled.

document.addEventListener('DOMContentLoaded', () => {
    if (typeof Vue === 'undefined' || !Vue.createApp) {
        return;
    }
    const el = document.getElementById('activity-app');
    if (!el) {
        return;
    }
    const endpoint = el.getAttribute('data-endpoint');
    Vue.createApp({
        data(){
            return { items:[], page:1, perPage:20, loading:false, allLoaded:false, total:0, autoRefresh:true, refreshTimer:null, filters:{type:''}, types:[] };
        },
        mounted(){ this.reload(); },
        beforeUnmount(){ this.stopLoop(); },
        methods:{
            fetchPage(reset = false){
                if (this.loading || (this.allLoaded && !reset)) {
                    return;
                } this.loading = true;
                const params = new URLSearchParams({ limit:this.perPage, page:this.page });
                if (this.filters.type) {
                    params.append('type', this.filters.type);
                }
                const url = endpoint + '?' + params.toString();
                fetch(url)
                .then(r => r.json())
                .then(j => {
                    if (reset) {
                        this.items = []; this.allLoaded = false; }
                    const arr = (j.activities && Array.isArray(j.activities)) ? j.activities : (j.activities ? .data || j.data ? .data || []);
                    if (arr.length < this.perPage) {
                        this.allLoaded = true;
                    }
                    this.items.push(...arr);
                    this.total = this.items.length;
                    arr.forEach(a => { if (a ? .type && !this.types.includes(a.type)) {
                            this.types.push(a.type);
                    } });
                })
            .catch(() => {})
            .finally(() => { this.loading = false; });
            },
            reload(){ this.page = 1; this.fetchPage(true); },
            next(){ this.page++; this.fetchPage(); },
            onScroll(){ const box = this.$refs.scrollBox; if (!box) {
                    return;
            } if (box.scrollTop + box.clientHeight >= box.scrollHeight - 40) {
                this.next(); } },
            startLoop(){ if (typeof window.ADMIN_AUTO_REFRESH !== 'undefined' && !window.ADMIN_AUTO_REFRESH) {
                    return;
            } if (this.autoRefresh) {
                this.reload();
            } },
            stopLoop(){ if (this.refreshTimer) {
                    clearInterval(this.refreshTimer);
            } },
            toggleAuto(){ this.autoRefresh = !this.autoRefresh; if (this.autoRefresh) {
                    this.reload();
            } },
            pretty(o){ try {
                    return JSON.stringify(o,null,2);} catch (e) {
                  return ''; } }
        }
    }).mount('#activity-app');
});
