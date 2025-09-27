window.onload = function () {
  loadRegions("personal");
  loadRegions("parent");
  loadRegions("emergency");
};

function loadRegions(type) {
  fetch("../handlers/get_regions.php")
    .then((response) => response.json())
    .then((data) => {
      const regionDropdown = document.getElementById(`${type}_region`);
      data.forEach((region) => {
        regionDropdown.innerHTML += `<option value="${region.id}">${region.name}</option>`;
      });
    });
}

function loadProvinces(type) {
  const regionId = document.getElementById(`${type}_region`).value;
  const provinceDropdown = document.getElementById(`${type}_province`);
  const municipalityDropdown = document.getElementById(`${type}_municipality`);
  const barangayDropdown = document.getElementById(`${type}_barangay`);

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
      fetch(`../handlers/get_provinces.php?region_id=${regionId}`)
        .then((res) => res.json())
        .then((data) => {
          data.forEach((province) => {
            provinceDropdown.innerHTML += `<option value="${province.id}">${province.name}</option>`;
          });
          provinceDropdown.disabled = false;
        });
    }
  }
}

function loadMunicipalities(type) {
  const provinceId = document.getElementById(`${type}_province`).value;
  const municipalityDropdown = document.getElementById(`${type}_municipality`);
  const barangayDropdown = document.getElementById(`${type}_barangay`);

  municipalityDropdown.innerHTML = `<option value="">Select Municipality/City</option>`;
  barangayDropdown.innerHTML = `<option value="">Select Barangay</option>`;

  barangayDropdown.disabled = true;

  if (provinceId) {
    fetch(`../handlers/get_municipalities.php?province_id=${provinceId}`)
      .then((res) => res.json())
      .then((data) => {
        data.forEach((municipality) => {
          municipalityDropdown.innerHTML += `<option value="${municipality.id}">${municipality.name}</option>`;
        });
        municipalityDropdown.disabled = false;
      });
  }
}

function loadBarangays(type) {
  const municipalityId = document.getElementById(`${type}_municipality`).value;
  const barangayDropdown = document.getElementById(`${type}_barangay`);

  barangayDropdown.innerHTML = `<option value="">Select Barangay</option>`;

  if (municipalityId) {
    fetch(`../handlers/get_barangays.php?municipality_id=${municipalityId}`)
      .then((res) => res.json())
      .then((data) => {
        data.forEach((barangay) => {
          barangayDropdown.innerHTML += `<option value="${barangay.id}">${barangay.name}</option>`;
        });
        barangayDropdown.disabled = false;
      });
  }
}

function loadMunicipalitiesFromRegion(type, regionId) {
  const municipalityDropdown = document.getElementById(`${type}_municipality`);
  const barangayDropdown = document.getElementById(`${type}_barangay`);

  municipalityDropdown.innerHTML = `<option value="">Select Municipality/City</option>`;
  barangayDropdown.innerHTML = `<option value="">Select Barangay</option>`;
  barangayDropdown.disabled = true;

  fetch(`../handlers/get_municipalities.php?region_id=${regionId}`)
    .then((res) => res.json())
    .then((data) => {
      data.forEach((municipality) => {
        municipalityDropdown.innerHTML += `<option value="${municipality.id}">${municipality.name}</option>`;
      });
      municipalityDropdown.disabled = false;
    });
}
