// main.gs - Fungsi Utama dan Menu

// ============= MENU & INITIALIZATION =============
function onOpen() {
  SpreadsheetApp.getUi()
    .createMenu('LarasAI Skin Analysis')
    .addItem('Start Analysis', 'startAnalysis')
    .addItem('View Analysis Status', 'viewAnalysisStatus')
    .addToUi();
}

function startAnalysis() {
  const ui = SpreadsheetApp.getUi();

  try {
    const response = ui.alert(
      'Start Analysis',
      'Start analyzing new customers?',
      ui.ButtonSet.YES_NO
    );

    if (response !== ui.Button.YES) return;

    const sheet = getQuestionnaireSheet();
    const lastRow = sheet.getLastRow();

    if (lastRow <= 2) {
      ui.alert('No customer data found');
      return;
    }

    const dataRange = sheet.getRange(3, 1, lastRow - 2, CONFIG.COLUMNS.STATUS);
    const data = dataRange.getValues();
    processCustomers(data, sheet);
  } catch (error) {
    logError('startAnalysis', error);
    ui.alert(`Error: ${error.message}`);
  }
}

// ============= CORE PROCESSING =============
function processCustomers(data, sheet) {
  logInfo('Starting customer processing');

  const processNextCustomer = (index) => {
    if (index >= data.length) {
      logSuccess('Customer processing completed');
      return;
    }

    const row = data[index];
    const rowNumber = index + 3;

    if (shouldProcessCustomer(row)) {
      processCustomerRow(row, rowNumber, sheet)
        .then(() => processNextCustomer(index + 1))
        .catch((error) => {
          handleError(error, sheet, rowNumber);
          processNextCustomer(index + 1);
        });
    } else {
      processNextCustomer(index + 1);
    }
  };

  processNextCustomer(0);
}

function shouldProcessCustomer(row) {
  const hasCloseup = row[CONFIG.COLUMNS.CLOSEUP_IMAGE - 1]?.trim();
  const status = row[CONFIG.COLUMNS.STATUS - 1];
  return hasCloseup && status !== "Completed";
}

async function processCustomerRow(row, rowNumber, sheet) {
  try {
    updateStatus(sheet, rowNumber, "Processing");
    logInfo(`Processing customer in row ${rowNumber}`);

    const customerData = parseCustomerData(row);
    validateCustomerData(customerData);

    const results = await runAnalysis(customerData);
    await saveResults(customerData, results);

    updateStatus(sheet, rowNumber, "Completed");
    logSuccess(`Completed analysis for row ${rowNumber}`);
  } catch (error) {
    handleError(error, sheet, rowNumber);
    throw error;
  }
}

// ============= ANALYSIS RUNNER =============
async function runAnalysis(customerData) {
  try {
    logInfo('Starting analysis for customer: ' + customerData.name);

    const [imageAnalysis, productRecs] = await Promise.all([
      analyzeImages(customerData),
      getProductRecommendations(customerData)
    ]);

    const aiAnalysis = await getAIAnalysis(customerData, imageAnalysis);

    logSuccess('Analysis completed for customer: ' + customerData.name);

    return {
      imageAnalysis,
      productRecommendations: productRecs,
      aiAnalysis,
      timestamp: new Date().toISOString()
    };
  } catch (error) {
    logError('runAnalysis', error);
    throw new Error(`Analysis failed: ${error.message}`);
  }
}

async function analyzeImages(customerData) {
  const results = {
    closeup: null,
    right: null,
    left: null
  };

  if (customerData.closeupImage) {
    results.closeup = await analyzeSingleImage(customerData.closeupImage, 'closeup');
  }

  if (customerData.rightImage) {
    results.right = await analyzeSingleImage(customerData.rightImage, 'right');
  }

  if (customerData.leftImage) {
    results.left = await analyzeSingleImage(customerData.leftImage, 'left');
  }

  return results;
}

async function analyzeSingleImage(imageUrl, type) {
  try {
    const response = await callFacePPAPI(imageUrl);
    return {
      type,
      result: response,
      timestamp: new Date().toISOString()
    };
  } catch (error) {
    logError(`analyzeSingleImage ${type}`, error);
    return {
      type,
      error: error.message,
      timestamp: new Date().toISOString()
    };
  }
}

async function getAIAnalysis(customerData, imageAnalysis) {
  logInfo('Getting AI analysis for customer: ' + customerData.name);

  const prompt = createAnalysisPrompt(customerData, imageAnalysis);

  try {
    const analysis = await callClaudeAPI(prompt);
    logSuccess('AI analysis received for customer: ' + customerData.name);
    return analysis;
  } catch (error) {
    logError('getAIAnalysis', error);
    throw error;
  }
}

// ============= RESULTS HANDLING =============
async function saveResults(customerData, analysisResults) {
  try {
    const resultsSheet = SpreadsheetApp.getActiveSpreadsheet()
      .getSheetByName(CONFIG.SHEETS.RESULTS);

    if (!resultsSheet) {
      throw new Error('Results sheet not found');
    }

    const formattedResults = formatResults(customerData, analysisResults);
    resultsSheet.appendRow(formattedResults);

    logSuccess('Results saved for customer: ' + customerData.name);
  } catch (error) {
    logError('saveResults', error);
    throw error;
  }
}

function formatResults(customerData, analysisResults) {
  const { imageAnalysis, productRecommendations, aiAnalysis } = analysisResults;

  // Ekstrak hasil analisis kulit dari respons Claude
  let hasilAnalisisKulit = "";

  if (aiAnalysis) {
    // Cari bagian analisis kulit di antara tag [SKIN_ANALYSIS]
    const analysisMatch = aiAnalysis.match(/\[SKIN_ANALYSIS\]([\s\S]*?)\[\/SKIN_ANALYSIS\]/);

    if (analysisMatch && analysisMatch[1]) {
      // Ekstrak bagian "Masalah Utama yang Perlu Ditangani"
      const masalahMatch = analysisMatch[1].match(/2\.\s*Masalah\s*Utama[^:]*:([\s\S]*?)(?=3\.|\[\/SKIN_ANALYSIS\])/i);

      if (masalahMatch && masalahMatch[1]) {
        // Bersihkan dan format hasil
        hasilAnalisisKulit = masalahMatch[1]
          .trim()
          .split('\n')
          .map(line => line.trim())
          .filter(line => line.length > 0)
          .join(", ");
      }
    }
  }

  // Jika tidak ada hasil dari Claude, gunakan hasil Face++
  if (!hasilAnalisisKulit) {
    const facePPAnalysis = imageAnalysis.closeup?.result ?
      processFacePPAnalysis(imageAnalysis.closeup) :
      { skinConcerns: [], confidenceScores: {} };

    hasilAnalisisKulit = facePPAnalysis.skinConcerns.join(", ");
  }

  // Format final untuk sheet
  const formattedResult = [
    new Date(),                                  // Tanggal (A)
    customerData.name,                           // Nama (B)
    calculateAge(customerData.birthDate),        // Umur (C)
    customerData.whatsappNumber || "",           // WhatsApp (D)
    JSON.stringify(imageAnalysis.closeup?.result || {}),  // Raw Face++ Result (E)
    aiAnalysis || '',                           // Claude Analysis (F)
    hasilAnalisisKulit,                         // Hasil Analisis Kulit dari Claude (G)
    productRecommendations.map(p => p.name).join(", ")   // Product Recommendations (H)
  ];

  console.log('Formatted results:', JSON.stringify(formattedResult, null, 2));
  return formattedResult;
}

// ============= VIEW STATUS =============
function viewAnalysisStatus() {
  const ui = SpreadsheetApp.getUi();
  const sheet = getQuestionnaireSheet();

  const lastRow = sheet.getLastRow();
  if (lastRow <= 2) {
    ui.alert('No data found');
    return;
  }

  const statusRange = sheet.getRange(3, CONFIG.COLUMNS.STATUS, lastRow - 2, 1);
  const statuses = statusRange.getValues();

  const counts = statuses.reduce((acc, [status]) => {
    acc[status || 'Pending'] = (acc[status || 'Pending'] || 0) + 1;
    return acc;
  }, {});

  let message = 'Analysis Status:\n\n';
  Object.entries(counts).forEach(([status, count]) => {
    message += `${status}: ${count}\n`;
  });

  ui.alert(message);
}

// analysis.gs - Fungsi Analisis dan Pemrosesan

// ============= DATA PROCESSING =============
function parseCustomerData(row) {
  return {
    name: getString(row[CONFIG.COLUMNS.NAME - 1]),
    birthDate: getString(row[CONFIG.COLUMNS.BIRTH_DATE - 1]),
    gender: getString(row[CONFIG.COLUMNS.GENDER - 1]),
    skinCondition: getArrayFromString(row[CONFIG.COLUMNS.SKIN_CONDITION - 1]),
    desiredCondition: getArrayFromString(row[CONFIG.COLUMNS.DESIRED_CONDITION - 1]),
    acneFrequency: getString(row[CONFIG.COLUMNS.ACNE_FREQUENCY - 1]),
    skinProblems: getString(row[CONFIG.COLUMNS.SKIN_PROBLEMS - 1]),
    specialCondition: getString(row[CONFIG.COLUMNS.SPECIAL_CONDITION - 1]),
    allergies: getArrayFromString(row[CONFIG.COLUMNS.ALLERGIES - 1]),
    retinolUsage: getString(row[CONFIG.COLUMNS.RETINOL_USAGE - 1]),
    ahaUsage: getString(row[CONFIG.COLUMNS.AHA_USAGE - 1]),
    doctorProduct: getString(row[CONFIG.COLUMNS.DOCTOR_PRODUCT - 1]),
    sunExposure: getString(row[CONFIG.COLUMNS.SUN_EXPOSURE - 1]),
    sleepTime: getString(row[CONFIG.COLUMNS.SLEEP_TIME - 1]),
    transportation: getString(row[CONFIG.COLUMNS.TRANSPORTATION - 1]),
    diet: getString(row[CONFIG.COLUMNS.DIET - 1]),
    rightImage: getString(row[CONFIG.COLUMNS.RIGHT_IMAGE - 1]),
    leftImage: getString(row[CONFIG.COLUMNS.LEFT_IMAGE - 1]),
    closeupImage: getString(row[CONFIG.COLUMNS.CLOSEUP_IMAGE - 1])
  };
}

// ============= FACE++ ANALYSIS =============
function processFacePPAnalysis(facePPResult) {
  try {
    if (!facePPResult || !facePPResult.result) {
      return {
        skinConcerns: [],
        confidenceScores: {}
      };
    }

    const result = facePPResult.result;
    const skinConcerns = [];
    const confidenceScores = {};

    // Fungsi helper untuk menambahkan concern berdasarkan nilai dan confidence
    const addConcern = (key, value, confidence, threshold = 0.7) => {
      if (confidence > threshold) {
        confidenceScores[key] = confidence;
        if (value === 1 || value === true) {
          switch (key) {
            case 'pores_left_cheek':
            case 'pores_right_cheek':
            case 'pores_forehead':
              skinConcerns.push('Pori-pori besar');
              break;
            case 'acne':
              skinConcerns.push('Jerawat aktif');
              break;
            case 'skin_spot':
              skinConcerns.push('Flek/noda kulit');
              break;
            case 'dark_circle':
              skinConcerns.push('Lingkaran hitam');
              break;
            case 'mole':
              skinConcerns.push('Tahi lalat');
              break;
          }
        }
      }
    };

    // Proses setiap properti hasil analisis
    Object.entries(result).forEach(([key, data]) => {
      if (data && typeof data === 'object' && 'confidence' in data && 'value' in data) {
        addConcern(key, data.value, data.confidence);
      }
    });

    // Analisis tipe kulit
    if (result.skin_type && result.skin_type.skin_type) {
      const skinTypes = {
        0: 'Kulit Normal',
        1: 'Kulit Kering',
        2: 'Kulit Berminyak',
        3: 'Kulit Kombinasi'
      };
      skinConcerns.push(skinTypes[result.skin_type.skin_type] || 'Tipe kulit tidak teridentifikasi');
    }

    return {
      skinConcerns: [...new Set(skinConcerns)], // Menghilangkan duplikat
      confidenceScores
    };
  } catch (error) {
    console.error('Error processing Face++ analysis:', error);
    return {
      skinConcerns: ['Error dalam analisis kulit'],
      confidenceScores: {}
    };
  }
}

// ============= API CALLS =============
async function callFacePPAPI(imageUrl) {
  const options = {
    method: "post",
    payload: {
      api_key: CONFIG.FACEPP.API_KEY,
      api_secret: CONFIG.FACEPP.API_SECRET,
      image_url: imageUrl,
    },
    muteHttpExceptions: true
  };

  const response = UrlFetchApp.fetch(CONFIG.FACEPP.ENDPOINT, options);
  const result = JSON.parse(response.getContentText());

  if (result.error_message) {
    throw new Error(`Face++ API Error: ${result.error_message}`);
  }

  return result;
}

async function callClaudeAPI(prompt) {
  // Log prompt details
  console.log('====== PROMPT DETAILS ======');
  console.log('Prompt content:', prompt);
  console.log('Prompt length:', prompt.length);

  const payload = {
    model: "claude-3-5-sonnet-20241022",
    max_tokens: 1024,
    messages: [{ role: "user", content: prompt }]
  };

  console.log('Formatted message:', JSON.stringify(payload.messages, null, 2));
  console.log('========================');

  const options = {
    method: "post",
    headers: {
      "anthropic-version": "2023-06-01",
      "x-api-key": CONFIG.ANTHROPIC.API_KEY,
      "Content-Type": "application/json",
    },
    payload: JSON.stringify(payload),
    muteHttpExceptions: true
  };

  try {
    const response = UrlFetchApp.fetch(CONFIG.ANTHROPIC.ENDPOINT, options);
    const result = JSON.parse(response.getContentText());

    if (result.error) {
      throw new Error(`Claude API Error: ${result.error.message}`);
    }

    return result?.content?.[0]?.text || '';

  } catch (error) {
    console.error('API Error:', error.toString());
    throw error;
  }
}

// ============= PRODUCT RECOMMENDATIONS =============
function getProductRecommendations(customerData) {
  try {
    const productsSheet = SpreadsheetApp.getActiveSpreadsheet()
      .getSheetByName(CONFIG.SHEETS.PRODUCTS);

    if (!productsSheet) {
      throw new Error("Products sheet not found");
    }

    const products = productsSheet.getDataRange().getValues();
    const [headers, ...productRows] = products;

    const recommendations = filterProductsForCustomer(productRows, customerData);
    return formatProductData(recommendations, headers);
  } catch (error) {
    logError('getProductRecommendations', error);
    return []; // Return empty array if error occurs
  }
}

function filterProductsForCustomer(products, customerData) {
  try {
    // Calculate scores for each product
    const scoredProducts = products.map(product => ({
      product,
      score: calculateProductScore(product, customerData)
    }));

    // Sort by score and take top 5
    return scoredProducts
      .filter(item => item.score > 0)
      .sort((a, b) => b.score - a.score)
      .slice(0, 5)
      .map(item => item.product);
  } catch (error) {
    logError('filterProductsForCustomer', error);
    return [];
  }
}

function calculateProductScore(product, customerData) {
  try {
    let score = 0;

    // Mendapatkan data produk
    const productName = (product[0] || '').toString().toLowerCase();
    const ingredients = (product[1] || '').toString().toLowerCase();
    const benefits = (product[2] || '').toString().toLowerCase();

    // Skor berdasarkan kondisi kulit saat ini
    customerData.skinCondition.forEach(condition => {
      if (benefits.includes(condition.toLowerCase())) score += 2;
    });

    // Skor berdasarkan kondisi yang diinginkan
    customerData.desiredCondition.forEach(condition => {
      if (benefits.includes(condition.toLowerCase())) score += 1.5;
    });

    // Pengurangan skor jika mengandung alergen
    customerData.allergies.forEach(allergen => {
      if (ingredients.includes(allergen.toLowerCase())) score -= 3;
    });

    // Penyesuaian skor berdasarkan kondisi khusus
    if (customerData.specialCondition) {
      const condition = customerData.specialCondition.toLowerCase();
      if (condition.includes('sensitive') && benefits.includes('sensitive')) score += 1;
      if (condition.includes('jerawat') && benefits.includes('acne')) score += 1;
      if (condition.includes('berminyak') && benefits.includes('oily')) score += 1;
      if (condition.includes('kering') && benefits.includes('dry')) score += 1;
    }

    // Penyesuaian berdasarkan masalah kulit
    if (customerData.skinProblems) {
      const problems = customerData.skinProblems.toLowerCase();
      if (problems.includes('flek') && benefits.includes('brightening')) score += 1;
      if (problems.includes('kusam') && benefits.includes('glowing')) score += 1;
      if (problems.includes('bekas') && benefits.includes('scar')) score += 1;
    }

    return Math.max(0, score); // Memastikan skor tidak negatif
  } catch (error) {
    logError('calculateProductScore', error);
    return 0;
  }
}

function formatProductData(products, headers) {
  return products.map(product => ({
    name: product[0] || '',
    ingredients: product[1] || '',
    benefits: product[2] || '',
    howToUse: product[3] || '',
    price: product[4] || ''
  }));
}

function createAnalysisPrompt(customerData, imageAnalysis) {
  const facePPAnalysis = imageAnalysis.closeup?.result ?
    processFacePPAnalysis(imageAnalysis.closeup) :
    { skinConcerns: [], confidenceScores: {} };

  return `
    Anda bertugas sebagai ahli perawatan kulit LarasAI, dengan tujuan memberikan analisis menyeluruh dan rekomendasi yang dipersonalisasi untuk pelanggan berdasarkan data yang diberikan. Dalam konteks ini, Anda akan menggunakan informasi tentang kondisi kulit, gaya hidup, dan hasil analisis dari deeplearning untuk menyusun laporan yang informatif dan bermanfaat.

Berikut adalah data pelanggan yang perlu Anda analisis:

Nama: ${customerData.name}
Umur: ${calculateAge(customerData.birthDate)}
Jenis Kelamin: ${customerData.gender}
Kondisi Kulit: ${customerData.skinCondition.join(", ")}
Keinginan: ${customerData.desiredCondition.join(", ")}
Masalah Kulit: ${customerData.skinProblems}
Kondisi Khusus: ${customerData.specialCondition}
Alergi: ${customerData.allergies.join(", ")}
Informasi tambahan mengenai gaya hidup pelanggan:

Paparan Matahari: ${customerData.sunExposure}
Jam Tidur: ${customerData.sleepTime}
Transportasi: ${customerData.transportation}
Diet: ${customerData.diet}
Hasil analisis kulit dari deeplearning yang harus Anda pertimbangkan adalah: ${facePPAnalysis.skinConcerns.join(", ")}

Detail tambahan dari analisis deeplearning termasuk: ${JSON.stringify(imageAnalysis.closeup?.result || {}, null, 2)}

Silakan berikan analisis dalam format berikut sertakan akurasi analisa hasil kulit : [SKIN_ANALYSIS]
1. Analisis Kondisi Kulit Saat Ini:
Kulit pelanggan memiliki jenis [tuliskan jenis kulit, misalnya berminyak/kering/normal]. Berdasarkan analisis dan keluhan yang dilaporkan, masalah utama meliputi [sebutkan masalah seperti jerawat, noda hitam, atau garis halus]. Area yang perlu perhatian khusus adalah [tuliskan area spesifik seperti pipi, dahi, atau dagu].

2. Masalah Utama yang Perlu Ditangani:
[Masalah 1, misalnya: Kulit kusam akibat paparan matahari.]
[Masalah 2, misalnya: Jerawat aktif di area T-zone.]
[Masalah lainnya berdasarkan analisis Face++.]

3. Rekomendasi Perawatan Kulit:
Gunakan pembersih wajah dengan [bahan yang disarankan, seperti asam salisilat].
Terapkan pelembap ringan yang cocok untuk kulit [jenis kulit].
Gunakan sunscreen SPF [angka SPF] untuk melindungi dari paparan matahari.
Tambahkan serum dengan kandungan [contoh: vitamin C] untuk mencerahkan kulit.

4.Saran Gaya Hidup:
Tingkatkan jam tidur hingga [minimal 7-8 jam per malam].
Kurangi paparan matahari langsung dengan menggunakan pelindung seperti topi atau payung.
Perbaiki pola makan dengan menambahkan [contoh: sayuran hijau, kacang-kacangan, omega-3].
Hindari stress berlebih untuk mengurangi risiko jerawat.

5. Catatan Khusus:
Pelanggan memiliki alergi terhadap [sebutkan alergen], sehingga hindari produk dengan kandungan tersebut. Perhatikan juga kondisi khusus seperti [misalnya, kehamilan] dalam memilih produk perawatan kulit.

[/SKIN_ANALYSIS]
  `;
}
