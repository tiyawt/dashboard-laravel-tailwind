const scannerScriptUrl =
    "https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js";

function loadScannerLibrary() {
    if (window.Html5Qrcode) return Promise.resolve();

    return new Promise((resolve, reject) => {
        const script = document.createElement("script");
        script.src = scannerScriptUrl;
        script.onload = resolve;
        script.onerror = () =>
            reject(new Error("Scanner library gagal dimuat."));
        document.head.appendChild(script);
    });
}

function assetInventoryPage(items, config) {
    return {
        items,
        scanUrl: config.scanUrl,
        baseUrl: config.baseUrl,
        selected: null,
        detailOpen: false,
        maintenancePage: 1,
        maintenancePerPage: 10,
        scannerOpen: false,
        lookupInProgress: false,
        qrScanner: null,
        scannerMessage: "Arahkan kamera ke barcode aset.",

        get detailFields() {
            return this.selected
                ? [
                      {
                          label: "No. Inventaris",
                          value: this.selected.no_inventaris,
                      },
                      {
                          label: "Nama Barang",
                          value: this.selected.nama_barang,
                      },
                      {
                          label: "Spesifikasi",
                          value: this.selected.spesifikasi,
                      },
                      { label: "Lantai", value: this.selected.lantai },
                      { label: "Lokasi", value: this.selected.lokasi },
                      {
                          label: "User",
                          value: this.selected.user || this.selected.nama_user,
                      },
                      {
                          label: "Kelengkapan",
                          value: this.selected.kelengkapan,
                      },
                      {
                          label: "Jumlah",
                          value: `${this.selected.jumlah || 0} Unit`,
                      },
                      {
                          label: "Tanggal Serah Terima",
                          value: this.formatDate(this.selected.tanggal_entry),
                      },
                      { label: "Keterangan", value: this.selected.keterangan },
                  ]
                : [];
        },

        get maintenancePageCount() {
            return Math.max(
                1,
                Math.ceil(
                    (this.selected?.maintenance_logs?.length || 0) /
                        this.maintenancePerPage,
                ),
            );
        },

        get maintenanceLogsPage() {
            const logs = this.selected?.maintenance_logs || [];
            const start = (this.maintenancePage - 1) * this.maintenancePerPage;
            return logs.slice(start, start + this.maintenancePerPage);
        },

        showDetail(id) {
            this.selected = this.items.find((item) => item.id === id);
            this.maintenancePage = 1;
            this.detailOpen = Boolean(this.selected);
        },

        detailUrl(id) {
            return `${this.baseUrl}/${id}`;
        },

        maintenanceUrl(id) {
            return `${this.detailUrl(id)}#maintenance-form`;
        },

        formatDate(value) {
            return value
                ? new Date(value).toLocaleDateString("id-ID", {
                      day: "numeric",
                      month: "long",
                      year: "numeric",
                  })
                : "-";
        },

        async openScanner() {
            this.scannerOpen = true;
            this.scannerMessage = "Memulai kamera...";

            try {
                await loadScannerLibrary();
                if (this.qrScanner) {
                    await this.closeScanner();
                    this.scannerOpen = true;
                }

                this.qrScanner = new window.Html5Qrcode("barcode-reader");
                const formats = window.Html5QrcodeSupportedFormats;
                const scannerConfig = {
                    fps: 10,
                    qrbox: { width: 300, height: 150 },
                    formatsToSupport: [
                        formats.QR_CODE,
                        formats.CODE_128,
                        formats.CODE_39,
                        formats.CODE_93,
                        formats.EAN_13,
                        formats.EAN_8,
                        formats.UPC_A,
                        formats.UPC_E,
                    ],
                };

                await this.qrScanner.start(
                    { facingMode: "environment" },
                    scannerConfig,
                    (decodedText) => this.lookup(decodedText),
                    () => {},
                );
                this.scannerMessage =
                    "Kamera aktif. Arahkan kamera ke barcode aset.";
            } catch (error) {
                console.error("Scanner error:", error);
                this.scannerMessage =
                    "Kamera tidak dapat digunakan. Pastikan izin kamera sudah diberikan.";
            }
        },

        findByCode() {
            this.lookup(this.$refs.manualCode.value);
        },

        async lookup(code) {
            const normalizedCode = String(code || "").trim();
            if (!normalizedCode || this.lookupInProgress) return;

            this.lookupInProgress = true;
            this.scannerMessage = `Barcode terbaca: ${normalizedCode}. Mencari data aset...`;
            const match = this.items.find(
                (item) =>
                    (item.no_inventaris || "").trim().toLowerCase() ===
                    normalizedCode.toLowerCase(),
            );

            if (match) {
                await this.closeScanner();
                this.showDetail(match.id);
                this.lookupInProgress = false;
                return;
            }

            try {
                const response = await fetch(
                    `${this.scanUrl}?code=${encodeURIComponent(normalizedCode)}`,
                    {
                        headers: { Accept: "application/json" },
                    },
                );
                const result = await response.json();

                if (response.ok && result.asset) {
                    this.selected = result.asset;
                    this.maintenancePage = 1;
                    await this.closeScanner();
                    this.detailOpen = true;
                } else {
                    this.scannerMessage = `Barcode terbaca, tetapi nomor inventaris "${normalizedCode}" tidak ditemukan.`;
                }
            } catch (error) {
                console.error("Lookup barcode error:", error);
                this.scannerMessage =
                    "Barcode terbaca, tetapi data aset gagal dimuat. Coba lagi.";
            } finally {
                this.lookupInProgress = false;
            }
        },

        async closeScanner() {
            this.scannerOpen = false;
            if (!this.qrScanner) return;

            try {
                await this.qrScanner.stop();
            } catch (error) {
                console.debug("Scanner sudah berhenti.");
            }
            try {
                this.qrScanner.clear();
            } catch (error) {
                console.debug("Scanner sudah dibersihkan.");
            }
            this.qrScanner = null;
        },
    };
}

window.assetInventoryPage = assetInventoryPage;
