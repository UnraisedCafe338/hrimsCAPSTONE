if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", function () {
    console.log("DOMContentLoaded event fired");
    try {
      console.log("Loading regions for personal, parent, and emergency");
      loadRegions("personal");
      loadRegions("parent");
      loadRegions("emergency");
    } catch (error) {
      console.error("Error initializing address dropdowns:", error);
    }
  });
} else {
  // DOM is already loaded
  console.log("DOM already loaded");
  try {
    console.log("Loading regions for personal, parent, and emergency");
    loadRegions("personal");
    loadRegions("parent");
    loadRegions("emergency");
  } catch (error) {
    console.error("Error initializing address dropdowns:", error);
  }
}

function loadRegions(type) {
  console.log(`Loading regions for ${type}`);
  fetch("/hrims/handlers/admin/get_regions.php")
    .then((response) => {
      console.log(`Response received for ${type} regions:`, response.status);
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      return response.json();
    })
    .then((data) => {
      console.log(`Data received for ${type} regions:`, data);
      const regionDropdown = document.getElementById(`${type}_region`);
      if (regionDropdown) {
        console.log(`Found region dropdown for ${type}`);
        // Clear existing options except the first one
        regionDropdown.innerHTML = '<option value="">Region</option>';
        data.forEach((region) => {
          const option = document.createElement("option");
          option.value = region.id;
          option.textContent = region.name;
          regionDropdown.appendChild(option);
        });
        console.log(`Populated ${data.length} regions for ${type}`);
      } else {
        console.error(`Region dropdown not found for type: ${type}`);
      }
    })
    .catch((error) => {
      console.error(`Error loading regions for ${type}:`, error);
    });
}

function loadProvinces(type) {
  const regionId = document.getElementById(`${type}_region`).value;
  const provinceDropdown = document.getElementById(`${type}_province`);
  const municipalityDropdown = document.getElementById(`${type}_municipality`);
  const barangayDropdown = document.getElementById(`${type}_barangay`);

  // Check if elements exist
  if (!provinceDropdown || !municipalityDropdown || !barangayDropdown) {
    console.error(`One or more dropdowns not found for type: ${type}`);
    return;
  }

  provinceDropdown.innerHTML = `<option value="">Select Province</option>`;
  municipalityDropdown.innerHTML = `<option value="">Select Municipality/City</option>`;
  barangayDropdown.innerHTML = `<option value="">Select Barangay</option>`;

  provinceDropdown.disabled = true;
  municipalityDropdown.disabled = true;
  barangayDropdown.disabled = true;

  if (regionId) {
    // Check if NCR (region_id 14) is selected
    if (parseInt(regionId) === 14) {
      // Skip province selection and go directly to municipalities
      loadMunicipalitiesFromRegion(type, regionId);
    } else {
      // Normal province loading for other regions
      fetch(`/hrims/handlers/admin/get_provinces.php?region_id=${regionId}`)
        .then((res) => {
          if (!res.ok) {
            throw new Error(`HTTP error! status: ${res.status}`);
          }
          return res.json();
        })
        .then((data) => {
          data.forEach((province) => {
            provinceDropdown.innerHTML += `<option value="${province.id}">${province.name}</option>`;
          });
          provinceDropdown.disabled = false;
        })
        .catch((error) => {
          console.error(`Error loading provinces for ${type}:`, error);
        });
    }
  }
}

function loadMunicipalities(type) {
  const provinceId = document.getElementById(`${type}_province`).value;
  const municipalityDropdown = document.getElementById(`${type}_municipality`);
  const barangayDropdown = document.getElementById(`${type}_barangay`);

  // Check if elements exist
  if (!municipalityDropdown || !barangayDropdown) {
    console.error(`One or more dropdowns not found for type: ${type}`);
    return;
  }

  municipalityDropdown.innerHTML = `<option value="">Select Municipality/City</option>`;
  barangayDropdown.innerHTML = `<option value="">Select Barangay</option>`;

  barangayDropdown.disabled = true;

  if (provinceId) {
    fetch(
      `/hrims/handlers/admin/get_municipalities.php?province_id=${provinceId}`
    )
      .then((res) => {
        if (!res.ok) {
          throw new Error(`HTTP error! status: ${res.status}`);
        }
        return res.json();
      })
      .then((data) => {
        data.forEach((municipality) => {
          municipalityDropdown.innerHTML += `<option value="${municipality.id}">${municipality.name}</option>`;
        });
        municipalityDropdown.disabled = false;
      })
      .catch((error) => {
        console.error(`Error loading municipalities for ${type}:`, error);
      });
  }
}

function loadBarangays(type) {
  const municipalityId = document.getElementById(`${type}_municipality`).value;
  const barangayDropdown = document.getElementById(`${type}_barangay`);

  // Check if element exists
  if (!barangayDropdown) {
    console.error(`Barangay dropdown not found for type: ${type}`);
    return;
  }

  barangayDropdown.innerHTML = `<option value="">Select Barangay</option>`;

  if (municipalityId) {
    fetch(
      `/hrims/handlers/admin/get_barangays.php?municipality_id=${municipalityId}`
    )
      .then((res) => {
        if (!res.ok) {
          throw new Error(`HTTP error! status: ${res.status}`);
        }
        return res.json();
      })
      .then((data) => {
        data.forEach((barangay) => {
          barangayDropdown.innerHTML += `<option value="${barangay.id}">${barangay.name}</option>`;
        });
        barangayDropdown.disabled = false;
      })
      .catch((error) => {
        console.error(`Error loading barangays for ${type}:`, error);
      });
  }
}

function loadMunicipalitiesFromRegion(type, regionId) {
  const municipalityDropdown = document.getElementById(`${type}_municipality`);
  const barangayDropdown = document.getElementById(`${type}_barangay`);

  // Check if elements exist
  if (!municipalityDropdown || !barangayDropdown) {
    console.error(`One or more dropdowns not found for type: ${type}`);
    return;
  }

  municipalityDropdown.innerHTML = `<option value="">Select Municipality/City</option>`;
  barangayDropdown.innerHTML = `<option value="">Select Barangay</option>`;
  barangayDropdown.disabled = true;

  fetch(`/hrims/handlers/admin/get_municipalities.php?region_id=${regionId}`)
    .then((res) => {
      if (!res.ok) {
        throw new Error(`HTTP error! status: ${res.status}`);
      }
      return res.json();
    })
    .then((data) => {
      data.forEach((municipality) => {
        municipalityDropdown.innerHTML += `<option value="${municipality.id}">${municipality.name}</option>`;
      });
      municipalityDropdown.disabled = false;
    })
    .catch((error) => {
      console.error(`Error loading municipalities for ${type}:`, error);
    });
}
