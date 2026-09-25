 <header class="bg-slate-900 text-white sticky top-0 z-40 shadow-md">
     <div class="max-w-7xl mx-auto px-4 py-3 flex flex-wrap justify-between items-center border-b border-slate-800">
         <div class="flex items-center space-x-3">
             <div class=" text-white p-2.5 rounded-xl font-black text-xl tracking-wider flex items-center justify-center shadow-lg align-items-center gap-2">
                 <!-- <img src="Tut Wuri Handayani.png" alt="" width="80px"> -->
                 <img src="<?= base_url('dashboard/Pemprov Sultra.png') ?>" alt="" width="80px">
             </div>
             <div>
                 <h1><strong>EDUVATION</strong></h1>
                 <h1 class="text-sm font-bold leading-tight tracking-wide">Dinas Pendidikan & Kebudayaan Provinsi Sulawesi Tenggara</h1>
                 <p class="text-xs text-slate-400">Ekosistem Inovasi & Praktik Baik Pendidikan Daerah</p>
             </div>
         </div>
         <div class="flex items-center space-x-3 mt-2 sm:mt-0 bg-slate-800/90 px-3 py-1.5 rounded-lg border border-slate-700">
             <span class="text-xs text-slate-300 font-medium"><i class="fa-solid fa-user-gear text-brand-400 mr-1"></i> Simulasi Akses:</span>
             <select id="roleSelector" onchange="switchRole(this.value)" class="bg-slate-900 text-xs text-white border border-slate-600 rounded px-2.5 py-1 focus:outline-none focus:ring-2 focus:ring-brand-500">
                 <option value="sekolah">Satuan Pendidikan (SMA/SMK/SLB)</option>
                 <option value="admin">Admin / Pengelola Dinas</option>
                 <option value="juri">Tim / Juri Kompetisi</option>
                 <option value="publik">Masyarakat / Pengguna Publik</option>
             </select>
             <span id="roleBadge" class="bg-emerald-500/20 text-emerald-400 text-[10px] px-2 py-0.5 rounded-full font-semibold border border-emerald-500/30">Sekolah</span>
         </div>
     </div>
     <nav class="bg-white border-b border-slate-200 text-slate-700 shadow-sm">
         <div class="max-w-7xl mx-auto px-4 flex space-x-1 sm:space-x-4 overflow-x-auto text-sm font-medium">
             <button onclick="navTo('beranda')" id="tab-beranda" class="py-3 px-3.5 whitespace-nowrap active-tab hover:text-brand-600 transition flex items-center">
                 <i class="fa-solid fa-house mr-2 text-xs"></i>Beranda
             </button>
             <button onclick="navTo('praktek-baik')" id="tab-praktek-baik" class="py-3 px-3.5 whitespace-nowrap hover:text-brand-600 transition flex items-center">
                 <i class="fa-solid fa-book-open mr-2 text-xs"></i>Praktek Baik
             </button>
             <button onclick="navTo('bank-inovasi')" id="tab-bank-inovasi" class="py-3 px-3.5 whitespace-nowrap hover:text-brand-600 transition flex items-center">
                 <i class="fa-solid fa-box-archive mr-2 text-xs"></i>Bank Inovasi
             </button>
             <button onclick="navTo('kompetisi')" id="tab-kompetisi" class="py-3 px-3.5 whitespace-nowrap hover:text-brand-600 transition flex items-center">
                 <i class="fa-solid fa-trophy mr-2 text-xs"></i>Kompetisi Inovasi
             </button>
             <button onclick="navTo('apresiasi')" id="tab-apresiasi" class="py-3 px-3.5 whitespace-nowrap hover:text-brand-600 transition flex items-center">
                 <i class="fa-solid fa-award mr-2 text-xs"></i>Apresiasi
             </button>
             <button onclick="navTo('monev')" id="tab-monev" class="py-3 px-3.5 whitespace-nowrap hover:text-brand-600 transition flex items-center">
                 <i class="fa-solid fa-chart-line mr-2 text-xs"></i>Monev
             </button>
             <button onclick="navTo('suara')" id="tab-suara" class="py-3 px-3.5 whitespace-nowrap hover:text-brand-600 transition flex items-center">
                 <i class="fa-solid fa-comments mr-2 text-xs"></i>SUARA
             </button>
         </div>
     </nav>
 </header>