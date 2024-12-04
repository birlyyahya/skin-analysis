<template>
    <FormWizard color="#0063F2" :start-index="1">
        <!-- YOU Kategori -->
        <TabContent title="Profil Pengguna">
            <div class="stepper-container">
                <div class="form-content">
                    <!-- Step 1: Profil Pengguna -->
                    <div v-if="currentStep === 0">
                        <div class="form-group">
                            <label>Hi, Nama kamu siapa?</label>
                            <input v-model="formData.nama" @keydown.enter.prevent="nextStep"
                                class="placeholder-shown:border-gray-100" type="text" placeholder="Nama..." required />
                            <div class="mt-5 text-sm text-center text-gray-700" id="user_avatar_help">Dapatkan analisis
                                kulit dan rekomendasi produk hanya dengan menjawab beberapa pertanyaan tentang dirimu,
                                gaya hidup, dan kondisi kulitmu.


                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Usia dan Lokasi -->
                    <div v-if="currentStep === 1">
                        <div class="form-group">
                            <label>Apa jenis kelaminmu?</label>
                            <select v-model="formData.jenisKelamin" autocomplete="country-name" name="gender"
                                @keydown.enter.prevent="nextStep" required>
                                <option value="" disabled selected>Pilih Gender</option>
                                <option value="pria">Pria</option>
                                <option value="wanita">Wanita</option>
                            </select>
                            <div class="mt-5 text-sm text-center text-gray-700" id="user_avatar_help">Secara biologis,
                                pria dan wanita memiliki perbedaan ketebalan, tingkat asam, serta kebutuhan eksfoliasi
                                kulit yang berbeda, sehingga produk yang direkomendasi dapat berbeda.
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Aktivitas Luar Ruangan -->
                    <div v-if="currentStep === 2">
                        <div class="form-group">
                            <label>Berapa usiamu?</label>
                            <select v-model="formData.usia" @keydown.enter.prevent="nextStep" required>
                                <option value="" disabled selected>Pilih rentang umurmu</option>
                                <option value="<20">&lt; 20 tahun</option>
                                <option value="20-30">20-30 tahun</option>
                                <option value="31-40">31-40 tahun</option>
                                <option value=">40">&gt; 40 tahun</option>
                            </select>
                            <div class="mt-5 text-sm text-center text-gray-700" id="user_avatar_help">Tidak perlu
                                malu... dengan umurmu membantu kami lebih memahami tentang kulitmu.
                            </div>
                        </div>
                    </div>
                    <div v-if="currentStep === 3">
                        <div class="form-group">
                            <label>Dimana lokasi kamu saat ini?</label>
                            <v-select :options="kotaList" label="name" v-model="formData.lokasi"
                                placeholder="Cari kotamu..." :reduce="kotaList => kotaList.name"
                                @keydown.enter.prevent="nextStep" />
                            <div @keydown.enter.prevent="nextStep" class="mt-5 text-sm text-center text-gray-700"
                                id="user_avatar_help">Jangan takut! kami
                                butuh wilayah kamu saat ini sebagai bahan analisa kami
                            </div>
                        </div>
                    </div>
                    <div v-if="currentStep === 4">
                        <div class="form-group">
                            <label>Apakah kamu sering berada di luar ruangan?</label>
                            <select v-model="formData.aktivitasLuarRuangan" required>
                                <option value="sering">Ya, sering (4-6 jam/hari)</option>
                                <option value="kadang">Kadang-kadang (1-3 jam/hari)</option>
                                <option value="jarang">Jarang (kurang dari 1 jam/hari)</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </TabContent>


        <!-- SKIN Kategori -->
        <TabContent title="Kondisi Kulit">
            <div class="stepper-container">
                <div class="form-content">
                    <div class="space-y-5 text-center">
                        <label>Seperti apa kondisi kulit kamu akhir-akhir ini?</label>
                        <ImageRadioButton v-model="formData.kondisiKulit" :options="kondisiKulit" />
                        <Transition name="fade">
                            <div v-if="formData.kondisiKulit === 'Sensitif'" class="duration-300 ease-in form-group">
                                <label>Apakah kamu memiliki kondisi kulit khusus?</label>
                                <select v-model="formData.kondisiKulitKhusus">
                                    <option value="eksim" selected>Eksim</option>
                                    <option value="rosacea">Rosacea</option>
                                    <option value="psoriasis">Psoriasis</option>
                                    <option value="melasma">Melasma</option>
                                    <option value="tidak">Tidak ada</option>
                                </select>
                            </div>
                        </Transition>
                    </div>

                    <div class="form-group" v-if="currentStep === 6">
                        <label>Apakah masalah Kulit yang Dialami?</label>
                        <div class="grid grid-cols-2">
                            <label v-for="masalah in masalahKulit" :key="masalah" style="text-align: left !important; font-size:1.2em; display:flex; align-items:center; font-weight:500;">
                                <input type="checkbox" :value="masalah" v-model="formData.masalahKulit" />
                                <p>{{ masalah }}</p>
                            </label>
                        </div>
                        <!-- Kondisional pertanyaan -->
                    </div>
                        <div v-if="formData.masalahKulit.includes('Jerawat')" class="form-group">
                            <label>Dalam sebulan, seberapa sering kamu mengalami masalah jerawat?</label>
                            <select v-model="formData.frekuensiJerawat">
                                <option value="1-2" selected >1-2 Kali</option>
                                <option value="3-4">3-4 Kali</option>
                                <option value="selalu">Selalu ada jerawat</option>
                            </select>
                        </div>
                </div>
            </div>
        </TabContent>

        <!-- HISTORY Kategori -->
        <TabContent title="Riwayat Skincare">
            <div class="form-content">
                <div class="form-group">
                    <label>Seberapa sensitif kulit Anda terhadap produk baru?</label>
                    <div>
                        <label v-for="produk in produkSkincare" :key="produk">
                            <input type="checkbox" :value="produk" v-model="formData.produkDigunakan" /> {{ produk }}
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Bagaimana reaksi kulit Anda terhadap produk sebelumnya?</label>
                    <select v-model="formData.reaksiKulit">
                        <option value="cocok">Cocok, tidak ada masalah</option>
                        <option value="kering">Kulit terasa kering</option>
                        <option value="berminyak">Kulit lebih berminyak</option>
                        <option value="iritasi">Kulit iritasi/kemerahan</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Apakah Anda alergi terhadap bahan tertentu?</label>
                    <div>
                        <label v-for="alergi in bahanAlergi" :key="alergi">
                            <input type="checkbox" :value="alergi" v-model="formData.alergi" /> {{ alergi }}
                        </label>
                    </div>
                    <input v-if="formData.alergi.includes('Lainnya')" v-model="formData.alergiLainnya"
                        placeholder="Sebutkan alergi lainnya" />
                </div>
            </div>
        </TabContent>

        <!-- LIFESTYLE Kategori -->
        <TabContent title="Gaya Hidup">
            <div class="form-content">
                <div class="form-group">
                    <label>Paparan Sinar Matahari:</label>
                    <select v-model="formData.paparanMatahari">
                        <option value="banyak">Banyak (4-6 jam)</option>
                        <option value="sedang">Sedang (1-3 jam)</option>
                        <option value="sedikit">Sedikit (&lt; 1 jam)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Jam Tidur:</label>
                    <select v-model="formData.jamTidur">
                        <option value="<4">&lt; 4 jam</option>
                        <option value="4-6">4-6 jam</option>
                        <option value="6-8">6-8 jam</option>
                        <option value=">8">&gt; 8 jam</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Kebiasaan Merokok:</label>
                    <select v-model="formData.merokok">
                        <option value="ya">Ya</option>
                        <option value="tidak">Tidak</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Moda Transportasi:</label>
                    <select v-model="formData.transportasi">
                        <option value="mobil">Mobil</option>
                        <option value="motor">Sepeda Motor</option>
                        <option value="bus">Bus</option>
                        <option value="kereta">Kereta</option>
                        <option value="jalan">Jalan Kaki</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Diet Khusus:</label>
                    <select v-model="formData.diet">
                        <option value="susu">Bebas Susu</option>
                        <option value="gluten">Bebas Gluten</option>
                        <option value="pescatarian">Pescatarian</option>
                        <option value="tidak">Tidak ada diet khusus</option>
                    </select>
                </div>
            </div>
        </TabContent>

        <!-- Hasil -->
        <TabContent title="Hasil">
            <div class="form-content">
                <h4>Kontak untuk Hasil Analisis</h4>
                <div class="form-group">
                    <label>Nomor WhatsApp:</label>
                    <input v-model="formData.whatsapp" type="tel" placeholder="Masukkan nomor WhatsApp" required />
                </div>

                <div class="form-group">
                    <label>Unggah Foto Selfie:</label>
                    <input type="file" multiple @change="handleFileUpload" accept="image/*" />
                </div>
            </div>
        </TabContent>

        <!-- Footer template -->
        <template v-slot:footer="props">
            <div class="flex justify-between px-10 py-12">
                <div class="wizard-footer-left">
                    <button v-if="currentStep !== 0 && currentStep !== 5" @click="prevStep"
                        class="inline-flex gap-5 items-center px-20 py-4 md:text-[1em] text-[0.9em] font-semibold tracking-wide text-white transition duration-150 ease-in-out bg-[#0063F2] border border-transparent rounded-lg hover:bg-[#003B91]">Kembali
                        Step</button>
                    <button v-else-if="currentStep === 5" @click.native="props.prevTab()" clas
                        class="inline-flex gap-5 items-center px-20 py-4 md:text-[1em] text-[0.9em] font-semibold tracking-wide text-white transition duration-150 ease-in-out bg-[#0063F2] border border-transparent rounded-lg hover:bg-[#003B91]">
                        Kembali
                    </button>
                </div>
                <div class="">
                    <div class="wizard-footer-right">
                        <button v-if="!handleNextTab" @click="nextStep" :disabled="!isFormValid"
                            class="disabled:opacity-50 disabled:cursor-not-allowed inline-flex gap-5 items-center px-20 py-4 md:text-[1em] text-[0.9em] font-semibold tracking-wide text-white transition duration-150 ease-in-out bg-[#0063F2] border border-transparent rounded-lg enabled:hover:bg-[#003B91]">Selanjutnya
                            Step</button>
                        <div class="wizard-footer-right" v-if="handleNextTab">
                            <button v-if="!props.isLastStep && handleNextTab"
                                @click.native="props.nextTab(); currentStep += 1; handleNextTab === false"
                                @keydown.enter.prevent="props.nextTab()" :disabled="!isFormValid"
                                class="disabled:opacity-50 disabled:cursor-not-allowed inline-flex gap-5 items-center px-20 py-4 md:text-[1em] text-[0.9em] font-semibold tracking-wide text-white transition duration-150 ease-in-out bg-[#0063F2] border border-transparent rounded-lg enabled:hover:bg-[#003B91]">
                                Selanjutnya Tab
                            </button>
                            <button v-else @click.native="submitForm"
                                class="inline-flex gap-5 items-center px-20 py-4 md:text-[1em] text-[0.9em] font-semibold tracking-wide text-white transition duration-150 ease-in-out bg-[#0063F2] border border-transparent rounded-lg hover:bg-[#003B91]">
                                Selesai
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </template>
    </FormWizard>
</template>

<script>
import { FormWizard, TabContent, WizardStep } from "vue3-form-wizard";
import vSelect from 'vue-select';
import "vue3-form-wizard/dist/style.css";
import 'vue-select/dist/vue-select.css';
import ImageRadioButton from "./ImageRadioButton.vue";

export default {
    name: "SkinCareWizard",
    components: {
        FormWizard,
        TabContent,
        WizardStep,
        ImageRadioButton,
        vSelect,
    },
    data() {
        return {
            currentStep: 0,
            handleNextTab: false,
            formData: {
                nama: '',
                jenisKelamin: '',
                usia: '',
                lokasi: '',
                aktivitasLuarRuangan: '',
                kondisiKulit: '',
                masalahKulit: [],
                frekuensiJerawat: '',
                kondisiKulitKhusus: '',
                kondisiKulitTerakhir: '',
                kulitSensitif: '',
                produkDigunakan: [],
                reaksiKulit: '',
                alergi: [],
                alergiLainnya: '',
                retinol: '',
                AHA_BHP_PHA: '',
                krimDokter: '',
                prioritasSkincare: '',
                anggaranSkincare: '',
                jenisProdukDisukai: '',
                paparanMatahari: '',
                jamTidur: '',
                merokok: '',
                transportasi: '',
                diet: '',
                selfie_closeup: '',
                selfie_left: '',
                selfie_right: '',
                whatsapp: '',
            },
            kotaList: [
            ],
            masalahKulit: [
                'Jerawat', 'Pori-pori besar', 'Kemerahan', 'Kusam',
                'Flek hitam/pigmentasi', 'Garis halus/kerutan', 'Sensitif/iritasi'
            ],
            produkSkincare: [
                'Sabun wajah', 'Toner', 'Serum', 'Pelembap',
                'Sunscreen', 'Masker', 'Tidak menggunakan apapun'
            ],
            bahanAlergi: [
                'Fragrance', 'Alkohol', 'Paraben', 'Lainnya'
            ],
            steps: [
                { title: 'namaa' },
                { title: 'jenisKelamin' },
                { title: 'usia' },
                { title: 'lcoation' },
                { title: 'aktivitasLuarRuangan' },
                { title: 'kondisiKulit' },
                { title: 'masalahKulit' },
                { title: 'frekuensiJerawat' },
                { title: 'kondisiKulitKhusus' },
                { title: 'produkDigunakan' },
                { title: 'reaksiKulit' },
                { title: 'alergi' },
                { title: 'alergiLainnya' },
                { title: 'paparanMatahari' },
                { title: 'jamTidur' },
                { title: 'merokok' },
                { title: 'transportasi' },
                { title: 'diet' },
                { title: 'whatsapp' },
                { title: 'selfie' },
            ],
            kondisiKulit: [
                {
                    value: "Normal",
                    label: "Normal",
                    image: "/storage/icon/kondisi_kulit/normal.png"
                },
                {
                    value: "Berminyak",
                    label: "Berminyak",
                    image: "/storage/icon/kondisi_kulit/oil.png"
                },
                {
                    value: "Kering",
                    label: "Kering",
                    image: "/storage/icon/kondisi_kulit/dry.png"
                },
                {
                    value: "Kombinasi",
                    label: "Kombinasi",
                    image: "/storage/icon/kondisi_kulit/kombinasi.png"
                },
                {
                    value: "Sensitif",
                    label: "Sensitif",
                    image: "/storage/icon/kondisi_kulit/sensitif.png"
                }
            ]
        };

    },
    computed: {
        isFormValid() {
            // Cek validitas form berdasarkan currentStep
            const currentField = Object.keys(this.formData)[this.currentStep]; // Dapatkan field berdasarkan currentStep
            if (this.currentStep == 4) {
                this.handleNextTab = true;
            } else {
                this.handleNextTab = false;
            }
            console.log(this.currentStep);
            console.log(this.handleNextTab);
            console.log(this.formData);
            return this.formData[currentField] !== ''; // Pastikan field tidak kosong
        },
    },
    methods: {
        async fetchCity() {
            try {
                const response = await fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json`);
                const data = await response.json(); // Tunggu data selesai diambil

                this.kotaList = data.map(province => province); // Mengambil nama provinsi
            } catch (error) {
                console.error('Error fetching cities:', error);
            }
        },
        handleEnter() {
            this.nextStep(); // Panggil nextStep saat Enter ditekan
        },
        nextStep() {
            if (this.currentStep < this.steps.length - 1) {
                if (this.currentStep == 4) {
                    this.handleNextTab = true;
                } else {
                    this.handleNextTab = false;
                }
                const currentField = Object.keys(this.formData)[this.currentStep]; // Dapatkan field berdasarkan currentStep
                console.log(this.formData[currentField]);
                console.log(this.currentStep);
                this.currentStep++;
            }
        },
        prevStep() {
            if (this.currentStep > 0) {
                this.currentStep--;
            }
        },
        handleFileUpload(event) {
            this.formData.selfie = Array.from(event.target.files);
        },
        submitForm() {
            // Validasi dan kirim data
            console.log('Form Data:', this.formData);
            // Tambahkan logika kirim data ke backend
            alert('Form berhasil dikirim!');
        }

    },
    mounted() {
        this.fetchCity(); // Panggil API saat komponen dimuat
    },
};
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.5s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

label {
    font-size: 1.5em;
    font-weight: 800;
    text-align: center;
    padding: 1.5em;
}

.form-group input:focus {
    border: 1px solid black !important;
}

.form-group input {
    text-align: center;
    font-size: 1.2em;
    font-weight: 500;
    width: 50%;
}


.v-select {
    width: 100%;
}

.form-content {
    max-width: 60%;
    margin: 0 auto;
    padding: 20px;
}

.form-group {
    width: 60%;
    margin: auto;
    margin-bottom: 15px;
}

input::placeholder {
    color: gray;
    opacity: 0.5;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
}

.form-group input,
.form-group select {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.form-group input[type="checkbox"] {
    width: auto;
    margin-right: 10px;
}

.wizard-footer-left .wizard-button {
    padding: 10px;
    border-radius: 6px;
    cursor: pointer;
}

.wizard-footer-right .wizard-button {
    padding: 10px;
    border-radius: 10px;
    cursor: pointer;
}

.wizard-footer-right .finish-button {
    padding: 15px 25px;
    border-radius: 6px;
    cursor: pointer;
    background-color: green !important;
    border: 0;
}

.v-radio-label {
    display: inline-flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    width: 100px;
    height: 100px;
    margin: 5px;
    border-radius: 4px;
    border: 1px solid #eee;
    transition: all 500ms;
}

.v-radio-active {
    box-shadow: 0 1px 5px 0 rgba(0, 0, 0, 0.2);
}
</style>
