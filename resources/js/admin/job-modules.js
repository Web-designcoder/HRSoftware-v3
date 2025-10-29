/* ======================================================
   Job Modules — Alpine Components for Campaign Details
   ====================================================== */

document.addEventListener('alpine:init', () => {

    /* ─────────────────────────────
       1. Campaign Details & Overviews
       ───────────────────────────── */
    Alpine.data('campaignDetails', (cfg) => ({
        job: cfg.job,
        logo: cfg.logo,
        background: cfg.background,
        assignment: cfg.assignment,
        flash: { details: '', details_ok: false, logo: '', logo_ok: false, overviews: '', overviews_ok: false },

        async saveDetails() {
            try {
                const res = await fetch(cfg.detailsUrl, {
                    method: 'PATCH',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': cfg.csrf },
                    body: JSON.stringify(this.job),
                });
                const json = await res.json();
                this.flash.details = json.ok ? 'Details updated successfully.' : 'Failed.';
                this.flash.details_ok = !!json.ok;
            } catch (e) {
                console.error(e);
                this.flash.details = 'Error saving details.';
                this.flash.details_ok = false;
            }
        },

        async saveOverviews() {
            try {
                const res = await fetch(cfg.overviewsUrl, {
                    method: 'PATCH',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': cfg.csrf },
                    body: JSON.stringify({ background: this.background, assignment: this.assignment }),
                });
                const json = await res.json();
                this.flash.overviews = json.ok ? 'Overview updated successfully.' : 'Failed.';
                this.flash.overviews_ok = !!json.ok;
            } catch (e) {
                console.error(e);
                this.flash.overviews = 'Error saving overview.';
                this.flash.overviews_ok = false;
            }
        },

        async uploadLogo(event) {
            const file = event.target.files[0];
            if (!file) return;
            const formData = new FormData();
            formData.append('company_logo', file);
            try {
                const res = await fetch(cfg.logoUrl, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': cfg.csrf },
                    body: formData
                });
                const json = await res.json();
                if (json.ok) {
                    this.logo = json.url;
                    this.flash.logo = 'Logo uploaded successfully.';
                    this.flash.logo_ok = true;
                } else throw new Error();
            } catch (e) {
                console.error(e);
                this.flash.logo = 'Failed to upload logo.';
                this.flash.logo_ok = false;
            }
        },
    }));


    /* ─────────────────────────────
       2. Campaign Documents
       ───────────────────────────── */
    Alpine.data('jobDocuments', (cfg) => ({
        docs: [],
        name: '',
        file: null,

        async init() { await this.refresh(); },

        async refresh() {
            try {
                const res = await fetch(cfg.fetchUrl, { headers: { 'Accept': 'application/json' }});
                const json = await res.json();
                this.docs = json.items ?? [];
            } catch (e) { console.error(e); }
        },

        selectFile(e) { this.file = e.target.files[0]; },

        async add() {
            if (!this.name || !this.file) return alert('Please select a file and name.');
            const form = new FormData();
            form.append('name', this.name);
            form.append('file', this.file);
            try {
                const res = await fetch(cfg.uploadUrl, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': cfg.csrf },
                    body: form
                });
                const json = await res.json();
                if (json.ok) this.docs.push(json.item);
                this.name = '';
                this.file = null;
            } catch (e) { console.error(e); }
        },

        async remove(id) {
            if (!confirm('Remove this document?')) return;
            try {
                await fetch(cfg.deleteBase.replace('/0', `/${id}`), {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': cfg.csrf }
                });
                this.docs = this.docs.filter(d => d.id !== id);
            } catch (e) { console.error(e); }
        },
    }));


    /* ─────────────────────────────
       3. Required Candidate Documents
       ───────────────────────────── */
    Alpine.data('jobRequiredDocs', (cfg) => ({
        docs: [],
        name: '',
        file: null,

        async init() { await this.refresh(); },

        async refresh() {
            try {
                const res = await fetch(cfg.fetchUrl, { headers: { 'Accept': 'application/json' }});
                const json = await res.json();
                this.docs = json.items ?? [];
            } catch (e) { console.error(e); }
        },

        selectFile(e) { this.file = e.target.files[0]; },

        async add() {
            if (!this.name) return alert('Name required.');
            const form = new FormData();
            form.append('name', this.name);
            if (this.file) form.append('file', this.file);
            try {
                const res = await fetch(cfg.uploadUrl, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': cfg.csrf },
                    body: form
                });
                const json = await res.json();
                if (json.ok) this.docs.push(json.item);
                this.name = '';
                this.file = null;
            } catch (e) { console.error(e); }
        },

        async remove(id) {
            if (!confirm('Delete this document?')) return;
            try {
                await fetch(cfg.deleteBase.replace('/0', `/${id}`), {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': cfg.csrf }
                });
                this.docs = this.docs.filter(d => d.id !== id);
            } catch (e) { console.error(e); }
        },
    }));


    /* ─────────────────────────────
       4. Questions
       ───────────────────────────── */
    Alpine.data('jobQuestions', (cfg) => ({
        questions: [],
        question: '',

        async init() { await this.refresh(); },

        async refresh() {
            try {
                const res = await fetch(cfg.fetchUrl);
                const json = await res.json();
                this.questions = json.items ?? [];
            } catch (e) { console.error(e); }
        },

        async add() {
            if (!this.question) return;
            try {
                const res = await fetch(cfg.createUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': cfg.csrf,
                    },
                    body: JSON.stringify({ question: this.question })
                });
                const json = await res.json();
                if (json.ok) {
                    this.questions.push(json.item);
                    this.question = '';
                }
            } catch (e) { console.error(e); }
        },

        async toggle(id) {
            try {
                const res = await fetch(cfg.toggleBase.replace('/0', `/${id}`), {
                    method: 'PATCH',
                    headers: { 'X-CSRF-TOKEN': cfg.csrf }
                });
                const json = await res.json();
                const q = this.questions.find(q => q.id === id);
                if (q) q.is_enabled = json.is_enabled;
            } catch (e) { console.error(e); }
        },

        async remove(id) {
            if (!confirm('Delete this question?')) return;
            try {
                await fetch(cfg.deleteBase.replace('/0', `/${id}`), {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': cfg.csrf }
                });
                this.questions = this.questions.filter(q => q.id !== id);
            } catch (e) { console.error(e); }
        },
    }));


    /* ─────────────────────────────
       5. Terms
       ───────────────────────────── */
    Alpine.data('termsBox', (cfg) => ({
        candidate: '',
        employer: '',
        msg: '',
        ok: false,

        async init() { await this.load(); },

        async load() {
            try {
                const res = await fetch(cfg.getUrl);
                const json = await res.json();
                this.candidate = json.terms_candidate ?? '';
                this.employer = json.terms_employer ?? '';
            } catch (e) { console.error(e); }
        },

        async save() {
            try {
                const res = await fetch(cfg.saveUrl, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': cfg.csrf
                    },
                    body: JSON.stringify({
                        terms_candidate: this.candidate,
                        terms_employer: this.employer
                    })
                });
                const json = await res.json();
                this.msg = json.ok ? 'Saved successfully.' : 'Failed.';
                this.ok = !!json.ok;
            } catch (e) {
                console.error(e);
                this.msg = 'Error saving.';
                this.ok = false;
            }
        }
    }));

    /* ─────────────────────────────
    6. Job Contacts
    ───────────────────────────── */
    Alpine.data('jobContacts', (cfg) => ({
        contacts: [],
        primaryContact: cfg.initialPrimary,
        allUsers: [],
        availableUsers: [],
        openModal: false,
        pendingIds: [],
        loading: false,
        search: '',
        selectAll: false,

        async init() {
            await this.refresh();
        },

        async refresh() {
            try {
                const [contactsRes, allRes] = await Promise.all([
                    fetch(cfg.fetchUrl, { headers: { 'Accept': 'application/json' } }),
                    fetch(cfg.allUsersUrl, { headers: { 'Accept': 'application/json' } })
                ]);

                const contactsJson = await contactsRes.json();
                const allUsersJson = await allRes.json();

                if (!contactsJson.ok) throw new Error('Failed to load contacts');
                this.contacts = contactsJson.contacts;
                this.primaryContact = contactsJson.primary ?? null;
                this.allUsers = allUsersJson.users ?? [];

                const attachedIds = this.contacts.map(c => Number(c.id));
                this.availableUsers = this.allUsers.filter(u => !attachedIds.includes(Number(u.id)));
            } catch (e) {
                console.error(e);
            }
        },

        get filteredUsers() {
            const term = this.search.toLowerCase();
            return this.availableUsers.filter(u =>
                u.name.toLowerCase().includes(term) ||
                u.email.toLowerCase().includes(term)
            );
        },

        toggleSelectAll() {
            if (this.selectAll) {
                this.pendingIds = this.filteredUsers.map(u => u.id);
            } else {
                this.pendingIds = [];
            }
        },

        async attachChecked() {
            if (this.pendingIds.length === 0) return;
            this.loading = true;
            try {
                const res = await fetch(cfg.attachUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': cfg.csrf,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ user_ids: this.pendingIds })
                });
                const json = await res.json();
                if (!json.ok) throw new Error('Attach failed');
                this.pendingIds = [];
                this.openModal = false;
                this.selectAll = false;
                toast('Contacts added successfully!');
                await this.refresh();
            } catch (e) {
                console.error(e);
                toast('Failed to add contacts', 'error');
            } finally {
                this.loading = false;
            }
        },

        async removeContact(id) {
            if (!confirm('Remove this contact?')) return;
            try {
                await fetch(`${cfg.detachBaseUrl}/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': cfg.csrf, 'Accept': 'application/json' }
                });
                toast('Contact removed');
                await this.refresh();
            } catch (e) {
                console.error(e);
                toast('Failed to remove contact', 'error');
            }
        },

        async updatePrimary() {
            try {
                const res = await fetch(cfg.primaryUrl, {
                    method: 'PATCH',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': cfg.csrf, 'Accept': 'application/json' },
                    body: JSON.stringify({ primary_contact_id: this.primaryContact })
                });
                const json = await res.json();
                if (!json.ok) throw new Error('Primary not updated');
                toast('Primary contact updated');
                await this.refresh();
            } catch (e) {
                console.error(e);
                toast('Failed to update primary', 'error');
            }
        }
    }));

});
