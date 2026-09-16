const scannerScriptUrl =
    "https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js";
const barcodeScriptUrl =
    "https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js";

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

function loadBarcodeLibrary() {
    if (window.JsBarcode) return Promise.resolve();

    return new Promise((resolve, reject) => {
        const script = document.createElement("script");
        script.src = barcodeScriptUrl;
        script.onload = resolve;
        script.onerror = () =>
            reject(new Error("Barcode library gagal dimuat."));
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
        pendingScan: "",
        pendingScanCount: 0,
        selectedIds: [],
        printInProgress: false,

        get allSelected() {
            return (
                this.items.length > 0 &&
                this.items.every((item) =>
                    this.selectedIds.includes(Number(item.id)),
                )
            );
        },

        toggleAll(checked) {
            const pageIds = this.items.map((item) => Number(item.id));
            this.selectedIds = checked
                ? [...new Set([...this.selectedIds, ...pageIds])]
                : this.selectedIds.filter((id) => !pageIds.includes(id));
        },

        escapeHtml(value) {
            return String(value ?? "-").replace(
                /[&<>'"]/g,
                (character) =>
                    ({
                        "&": "&amp;",
                        "<": "&lt;",
                        ">": "&gt;",
                        "'": "&#039;",
                        '"': "&quot;",
                    })[character],
            );
        },

        async printBarcodes() {
            const selectedItems = this.items.filter((item) =>
                this.selectedIds.includes(Number(item.id)),
            );
            if (!selectedItems.length || this.printInProgress) return;

            this.printInProgress = true;
            const printWindow = window.open(
                "",
                "_blank",
                "width=900,height=700",
            );
            if (!printWindow) {
                this.printInProgress = false;
                window.alert(
                    "Popup cetak diblokir browser. Izinkan popup untuk halaman ini lalu coba lagi.",
                );
                return;
            }

            try {
                await loadBarcodeLibrary();
                const labels = selectedItems
                    .map((item) => {
                        const inventoryNumber = String(
                            item.no_inventaris || "",
                        ).trim();
                        const barcode = document.createElementNS(
                            "http://www.w3.org/2000/svg",
                            "svg",
                        );
                        barcode.setAttribute(
                            "preserveAspectRatio",
                            "xMidYMid meet",
                        );
                        window.JsBarcode(barcode, inventoryNumber, {
                            format: "CODE128",
                            displayValue: false,
                            width: 2,
                            height: 120,
                            margin: 4,
                        });

                        return `<article class="label">
                        <h1>RS Khusus Ginjal NY RA Habibie</h1>
                        ${barcode.outerHTML}
                        <h2>${this.escapeHtml(item.nama_barang || "-")}</h2>
                        <p class="inventory-number">${this.escapeHtml(inventoryNumber)}</p>
                        <p><strong>Lokasi:</strong> ${this.escapeHtml(item.lokasi || "-")}</p>
                    </article>`;
                    })
                    .join("");
                printWindow.document
                    .write(`<!doctype html><html><head><title>Cetak Barcode Aset</title><style>
                    @page { size: A4; margin: 0; }
                    * { box-sizing: border-box; }
                    body { margin: 0; font-family: Arial, sans-serif; color: #111827; }
                    .sheet { display: grid; grid-template-columns: repeat(2, 70mm); grid-auto-rows: 50mm; width: 142mm; gap: 2mm; margin: 8mm; align-items: start; }
                    .label { box-sizing: border-box; flex: 0 0 70mm; width: 70mm; min-width: 70mm; max-width: 70mm; height: 50mm; min-height: 50mm; max-height: 50mm; break-inside: avoid; overflow: hidden; border: 1px solid #d1d5db; padding: 3mm; text-align: center; }
                    h1 { margin: 0 0 1mm; font-size: 11pt; font-weight: 400; white-space: nowrap; }
                    h2 { margin: 0; font-size: 12pt; font-weight: 400; line-height: 1.1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
                    svg { display: block; width: 59mm !important; height: 23mm !important; max-width: 59mm; margin: 0 auto; shape-rendering: crispEdges; }
                    p { margin: 1mm 0 0; font-size: 8pt; line-height: 1.1; text-align: center; }
                    .inventory-number { font-size: 12pt; }
                    @media print { .label { border-color: #9ca3af; } }
                </style></head><body><main class="sheet">${labels}</main></body></html>`);
                printWindow.document.close();
                printWindow.focus();
                printWindow.print();
            } catch (error) {
                console.error("Barcode print error:", error);
                printWindow.close();
                window.alert(
                    "Barcode gagal disiapkan. Pastikan koneksi internet tersedia dan popup tidak diblokir.",
                );
            } finally {
                this.printInProgress = false;
            }
        },

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
                const formats = window.Html5QrcodeSupportedFormats || {};
                const scannerConfig = {
                    fps: 15,
                    qrbox: { width: 320, height: 180 },
                    formatsToSupport: [formats.CODE_128].filter(
                        (format) => format !== undefined,
                    ),
                };

                await this.qrScanner.start(
                    { facingMode: "environment" },
                    scannerConfig,
                    (decodedText) => this.confirmScan(decodedText),
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

        confirmScan(code) {
            const normalizedCode = String(code || "").trim();
            if (!normalizedCode || this.lookupInProgress) return;

            if (normalizedCode === this.pendingScan) {
                this.pendingScanCount += 1;
            } else {
                this.pendingScan = normalizedCode;
                this.pendingScanCount = 1;
            }

            if (this.pendingScanCount < 2) {
                this.scannerMessage =
                    "Barcode terbaca. Tahan kamera sebentar untuk konfirmasi...";
                return;
            }

            this.pendingScan = "";
            this.pendingScanCount = 0;
            this.lookup(normalizedCode);
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
