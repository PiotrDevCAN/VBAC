function pesDescriptionHover() {
	$(".btnPesDescription").tooltip({
		content:
			"<p>SPRH (Level 1)</p>" +
			"Will have Privliged Access/Administration to Storage, backup and Cloud infrastructure. Anyone who has the capability to shut down Cloud infrastructure, Storage or Bank-wide backup infrastructure. Anyone who's access could allow deletion of large volumes of data, either on SANs or backups. Anyone with access that could inflict such levels of damage on the Bank that it might cease to operate." +
			"<p>SRH (Level 2)</p>" +
			"SRH - Will have Access to the Lloyds network, (email etc) but no privilege access to the banks IT infrastructure" +
			"<p>No Client Access (Level 2)</p>" +
			"Will not have access to Lloyds network or systems",
		tooltipClass: "toolTipDetails",
	});
}

export { pesDescriptionHover as default };