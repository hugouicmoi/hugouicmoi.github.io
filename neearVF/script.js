function openModal(modal) {
    if (modal) {
        modal.classList.add("show");
    }
}

function closeModal(modal) {
    if (modal) {
        modal.classList.remove("show");
    }
}

function random(min, max) {
    return Math.random() * (max - min) + min;
}

document.addEventListener("DOMContentLoaded", function () {
    const authModal = document.getElementById("authModal");
    const openAuth = document.getElementById("openAuth");
    const openAuth2 = document.getElementById("openAuth2");
    const closeAuth = document.getElementById("closeAuth");

    const showLoginBtn = document.getElementById("showLogin");
    const showRegisterBtn = document.getElementById("showRegister");
    const loginPanel = document.getElementById("loginPanel");
    const registerPanel = document.getElementById("registerPanel");

    const scoreModal = document.getElementById("scoreModal");
    const openScoreBtn = document.getElementById("openScoreModal");
    const closeScoreBtn = document.getElementById("closeScoreModal");

    const editProfileModal = document.getElementById("editProfileModal");
    const commentModal = document.getElementById("commentModal");
    const contactModal = document.getElementById("contactModal");
    const userProfileModal = document.getElementById("userProfileModal");
    const postDetailModal = document.getElementById("postDetailModal");
    const reviewDetailModal = document.getElementById("reviewDetailModal");
    const searchProfileModal = document.getElementById("searchProfileModal");

    const allCommentsModal = document.getElementById("allCommentsModal");
    const allCommentsTitle = document.getElementById("allCommentsTitle");
    const allCommentsList = document.getElementById("allCommentsList");

    function setAuthTab(tab) {
        if (!showLoginBtn || !showRegisterBtn || !loginPanel || !registerPanel) {
            return;
        }

        if (tab === "register") {
            showRegisterBtn.classList.add("active");
            showLoginBtn.classList.remove("active");
            registerPanel.classList.add("active");
            loginPanel.classList.remove("active");
        } else {
            showLoginBtn.classList.add("active");
            showRegisterBtn.classList.remove("active");
            loginPanel.classList.add("active");
            registerPanel.classList.remove("active");
        }
    }

    if (openAuth) {
        openAuth.addEventListener("click", function () {
            setAuthTab("login");
            openModal(authModal);
        });
    }

    if (openAuth2) {
        openAuth2.addEventListener("click", function () {
            setAuthTab("register");
            openModal(authModal);
        });
    }

    if (closeAuth) {
        closeAuth.addEventListener("click", function () {
            closeModal(authModal);
        });
    }

    if (authModal) {
        authModal.addEventListener("click", function (e) {
            if (e.target === authModal) {
                closeModal(authModal);
            }
        });
    }

    if (showLoginBtn) {
        showLoginBtn.addEventListener("click", function () {
            setAuthTab("login");
        });
    }

    if (showRegisterBtn) {
        showRegisterBtn.addEventListener("click", function () {
            setAuthTab("register");
        });
    }

    if (typeof window.defaultAuthTab !== "undefined") {
        setAuthTab(window.defaultAuthTab);
    } else {
        setAuthTab("login");
    }

    const authMessage = document.querySelector("#authModal .message");
    if (authMessage && authModal) {
        openModal(authModal);
    }

    if (openScoreBtn) {
        openScoreBtn.addEventListener("click", function () {
            openModal(scoreModal);
        });
    }

    if (closeScoreBtn) {
        closeScoreBtn.addEventListener("click", function () {
            closeModal(scoreModal);
        });
    }

    if (scoreModal) {
        scoreModal.addEventListener("click", function (e) {
            if (e.target === scoreModal) {
                closeModal(scoreModal);
            }
        });
    }

    document.querySelectorAll("[data-open-modal]").forEach((button) => {
        button.addEventListener("click", function () {
            const modalId = this.getAttribute("data-open-modal");
            const modal = document.getElementById(modalId);
            openModal(modal);
        });
    });

    document.querySelectorAll("[data-close-modal]").forEach((button) => {
        button.addEventListener("click", function () {
            const modalId = this.getAttribute("data-close-modal");
            const modal = document.getElementById(modalId);
            closeModal(modal);
        });
    });

    [editProfileModal, commentModal, contactModal, userProfileModal, postDetailModal, reviewDetailModal, searchProfileModal, allCommentsModal].forEach((modal) => {
        if (!modal) return;

        modal.addEventListener("click", function (e) {
            if (e.target === modal) {
                closeModal(modal);
            }
        });
    });

    document.querySelectorAll(".js-open-score").forEach((button) => {
        button.addEventListener("click", function () {
            openModal(scoreModal);
        });
    });

    function escapeHtml(value) {
        return String(value ?? "")
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function getInitials(name) {
        return String(name || "")
            .split(" ")
            .filter(Boolean)
            .map((part) => part.charAt(0).toUpperCase())
            .join("")
            .slice(0, 2);
    }

    function normalizeText(value) {
        return String(value || "")
            .normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "")
            .toLowerCase()
            .trim();
    }

    function openAllCommentsModal(userName, comments) {
        if (!allCommentsModal || !allCommentsList || !allCommentsTitle) return;

        allCommentsTitle.textContent = `Tous les commentaires de ${userName}`;

        if (!comments || !comments.length) {
            allCommentsList.innerHTML = `<p class="empty-comments">Aucun commentaire pour le moment.</p>`;
        } else {
            allCommentsList.innerHTML = comments.map((item) => `
                <div class="comment-preview-item">
                    <h5>${escapeHtml(item.author_name)}</h5>
                    <p>${escapeHtml(item.comment)}</p>
                </div>
            `).join("");
        }

        openModal(allCommentsModal);
    }

    const commentTargetName = document.getElementById("commentTargetName");
    const commentTargetUserId = document.getElementById("commentTargetUserId");

    document.querySelectorAll(".js-open-comment").forEach((button) => {
        button.addEventListener("click", function () {
            const userId = this.dataset.userId || "";
            const userName = this.dataset.userName || "ce profil";

            if (commentTargetName) commentTargetName.textContent = userName;
            if (commentTargetUserId) commentTargetUserId.value = userId;

            openModal(commentModal);
        });
    });

    document.querySelectorAll(".js-open-all-comments").forEach((button) => {
        button.addEventListener("click", function () {
            let comments = [];

            try {
                comments = JSON.parse(this.dataset.comments || "[]");
            } catch (e) {
                comments = [];
            }

            openAllCommentsModal(this.dataset.userName || "Profil", comments);
        });
    });

    const contactTargetName = document.getElementById("contactTargetName");
    const contactTargetUserId = document.getElementById("contactTargetUserId");
    const contactSavedName = document.getElementById("contactSavedName");
    const contactSavedJob = document.getElementById("contactSavedJob");
    const contactSavedEmail = document.getElementById("contactSavedEmail");
    const contactSavedPhone = document.getElementById("contactSavedPhone");

    document.querySelectorAll(".js-open-contact").forEach((button) => {
        button.addEventListener("click", function () {
            if (contactTargetName) contactTargetName.textContent = this.dataset.userName || "ce profil";
            if (contactTargetUserId) contactTargetUserId.value = this.dataset.userId || "";
            if (contactSavedName) contactSavedName.value = this.dataset.userName || "";
            if (contactSavedJob) contactSavedJob.value = this.dataset.userJob || "";
            if (contactSavedEmail) contactSavedEmail.value = this.dataset.userEmail || "";
            if (contactSavedPhone) contactSavedPhone.value = this.dataset.userPhone || "";

            openModal(contactModal);
        });
    });

    const userProfileName = document.getElementById("userProfileName");
    const userProfileJob = document.getElementById("userProfileJob");
    const userProfileBadge = document.getElementById("userProfileBadge");
    const userProfilePoints = document.getElementById("userProfilePoints");
    const userProfileBio = document.getElementById("userProfileBio");
    const userProfileEmail = document.getElementById("userProfileEmail");
    const userProfilePhone = document.getElementById("userProfilePhone");
    const userProfileCommentsList = document.getElementById("userProfileCommentsList");
    const userProfileAvatarWrap = document.getElementById("userProfileAvatarWrap");
    const userProfileSeeMoreComments = document.getElementById("userProfileSeeMoreComments");

    let currentOpenedProfile = null;

    function renderUserProfile(profile) {
        if (!profile) return;

        currentOpenedProfile = {
            userId: profile.id,
            userName: profile.fullname,
            userJob: profile.job,
            userEmailValue: profile.contact_email || "",
            userPhoneValue: profile.contact_phone || "",
            comments: Array.isArray(profile.comments) ? profile.comments : []
        };

        if (userProfileName) userProfileName.textContent = profile.fullname || "";
        if (userProfileJob) userProfileJob.textContent = profile.job || "";
        if (userProfileBadge) userProfileBadge.textContent = profile.badge || "";
        if (userProfilePoints) userProfilePoints.textContent = `${profile.points || 0} points`;
        if (userProfileBio) userProfileBio.textContent = profile.bio || "";
        if (userProfileEmail) userProfileEmail.textContent = profile.contact_email || "-";
        if (userProfilePhone) userProfilePhone.textContent = profile.contact_phone || "-";

        if (userProfileAvatarWrap) {
            if (profile.profile_image) {
                userProfileAvatarWrap.innerHTML = `<img src="${profile.profile_image}" alt="${profile.fullname}" class="profile-photo detail-photo">`;
            } else {
                userProfileAvatarWrap.innerHTML = `<div class="avatar-circle big-avatar">${escapeHtml(getInitials(profile.fullname || ""))}</div>`;
            }
        }

        if (userProfileCommentsList) {
            const comments = Array.isArray(profile.comments) ? profile.comments : [];
            const visibleComments = comments.slice(0, 3);

            if (!visibleComments.length) {
                userProfileCommentsList.innerHTML = `<p class="empty-comments">Aucun commentaire pour le moment.</p>`;
            } else {
                userProfileCommentsList.innerHTML = visibleComments.map((item) => `
                    <div class="comment-preview-item">
                        <h5>${escapeHtml(item.author_name)}</h5>
                        <p>${escapeHtml(item.comment)}</p>
                    </div>
                `).join("");
            }

            if (userProfileSeeMoreComments) {
                if (comments.length > 3) {
                    userProfileSeeMoreComments.style.display = "inline-flex";
                    userProfileSeeMoreComments.onclick = function () {
                        openAllCommentsModal(profile.fullname || "Profil", comments);
                    };
                } else {
                    userProfileSeeMoreComments.style.display = "none";
                    userProfileSeeMoreComments.onclick = null;
                }
            }
        }

        openModal(userProfileModal);
    }

    document.querySelectorAll(".js-open-user-profile").forEach((button) => {
        button.addEventListener("click", function () {
            let comments = [];

            try {
                comments = JSON.parse(this.dataset.userComments || "[]");
            } catch (e) {
                comments = [];
            }

            renderUserProfile({
                id: this.dataset.userId || "",
                fullname: this.dataset.userName || "",
                job: this.dataset.userJob || "",
                bio: this.dataset.userBio || "",
                badge: this.dataset.userBadge || "",
                points: this.dataset.userPoints || "0",
                contact_email: this.dataset.userEmail || "",
                contact_phone: this.dataset.userPhone || "",
                profile_image: this.dataset.userImage || "",
                comments
            });
        });
    });

    document.querySelectorAll(".js-comment-from-profile").forEach((button) => {
        button.addEventListener("click", function () {
            if (!currentOpenedProfile) return;

            if (commentTargetName) commentTargetName.textContent = currentOpenedProfile.userName;
            if (commentTargetUserId) commentTargetUserId.value = currentOpenedProfile.userId;

            closeModal(userProfileModal);
            openModal(commentModal);
        });
    });

    document.querySelectorAll(".js-contact-from-profile").forEach((button) => {
        button.addEventListener("click", function () {
            if (!currentOpenedProfile) return;

            if (contactTargetName) contactTargetName.textContent = currentOpenedProfile.userName;
            if (contactTargetUserId) contactTargetUserId.value = currentOpenedProfile.userId;
            if (contactSavedName) contactSavedName.value = currentOpenedProfile.userName;
            if (contactSavedJob) contactSavedJob.value = currentOpenedProfile.userJob;
            if (contactSavedEmail) contactSavedEmail.value = currentOpenedProfile.userEmailValue;
            if (contactSavedPhone) contactSavedPhone.value = currentOpenedProfile.userPhoneValue;

            closeModal(userProfileModal);
            openModal(contactModal);
        });
    });

    const postDetailTag = document.getElementById("postDetailTag");
    const postDetailAuthor = document.getElementById("postDetailAuthor");
    const postDetailMeta = document.getElementById("postDetailMeta");
    const postDetailContent = document.getElementById("postDetailContent");

    document.querySelectorAll(".js-open-post").forEach((post) => {
        post.addEventListener("click", function () {
            if (postDetailTag) postDetailTag.textContent = this.dataset.postTag || "Post";
            if (postDetailAuthor) postDetailAuthor.textContent = this.dataset.postAuthor || "";
            if (postDetailMeta) postDetailMeta.textContent = `${this.dataset.postJob || ""} · ${this.dataset.postHandle || ""}`;
            if (postDetailContent) postDetailContent.textContent = this.dataset.postContent || "";

            openModal(postDetailModal);
        });
    });

    const reviewDetailTitle = document.getElementById("reviewDetailTitle");
    const reviewDetailSource = document.getElementById("reviewDetailSource");
    const reviewDetailContent = document.getElementById("reviewDetailContent");

    document.querySelectorAll(".js-open-review").forEach((review) => {
        review.addEventListener("click", function () {
            if (reviewDetailTitle) reviewDetailTitle.textContent = this.dataset.reviewTitle || "";
            if (reviewDetailSource) reviewDetailSource.textContent = this.dataset.reviewSource || "";
            if (reviewDetailContent) reviewDetailContent.textContent = this.dataset.reviewText || "";

            openModal(reviewDetailModal);
        });
    });

    const profileSearchInput = document.getElementById("profileSearchInput");
    const profileSearchResults = document.getElementById("profileSearchResults");
    const searchProfiles = Array.isArray(window.searchProfiles) ? window.searchProfiles : [];

    function renderSearchResults(items) {
        if (!profileSearchResults) return;

        if (!items.length) {
            profileSearchResults.innerHTML = `<div class="search-result-empty">Aucun profil trouvé.</div>`;
            return;
        }

        profileSearchResults.innerHTML = items.map((profile) => {
            const safeName = escapeHtml(profile.fullname);
            const safeJob = escapeHtml(profile.job);
            const safeBadge = escapeHtml(profile.badge);
            const safePoints = escapeHtml(profile.points);
            const safeImage = escapeHtml(profile.profile_image || "");
            const initials = escapeHtml(getInitials(profile.fullname));

            const avatar = safeImage
                ? `<img src="${safeImage}" alt="${safeName}" class="search-result-photo">`
                : `<div class="avatar-circle search-result-avatar">${initials}</div>`;

            return `
                <button type="button" class="search-result-item" data-profile-id="${escapeHtml(profile.id)}">
                    ${avatar}
                    <div class="search-result-content">
                        <h4>${safeName}</h4>
                        <p>${safeJob}</p>
                        <span>${safeBadge} · ${safePoints} pts</span>
                    </div>
                </button>
            `;
        }).join("");

        profileSearchResults.querySelectorAll(".search-result-item").forEach((button) => {
            button.addEventListener("click", function () {
                const profileId = String(this.dataset.profileId || "");
                const profile = searchProfiles.find((item) => String(item.id) === profileId);

                if (!profile) return;

                closeModal(searchProfileModal);
                renderUserProfile(profile);
            });
        });
    }

    function filterProfiles(query) {
        const normalizedQuery = normalizeText(query);

        if (!normalizedQuery) {
            return searchProfiles;
        }

        return searchProfiles.filter((profile) => {
            const fullname = normalizeText(profile.fullname);
            const job = normalizeText(profile.job);
            const bio = normalizeText(profile.bio);
            const badge = normalizeText(profile.badge);

            return (
                fullname.includes(normalizedQuery) ||
                job.includes(normalizedQuery) ||
                bio.includes(normalizedQuery) ||
                badge.includes(normalizedQuery)
            );
        });
    }

    if (profileSearchInput) {
        profileSearchInput.addEventListener("input", function () {
            renderSearchResults(filterProfiles(this.value));
        });

        profileSearchInput.addEventListener("focus", function () {
            renderSearchResults(filterProfiles(this.value));
        });
    }

    document.querySelectorAll('[data-open-modal="searchProfileModal"]').forEach((button) => {
        button.addEventListener("click", function () {
            if (profileSearchInput) {
                profileSearchInput.value = "";
                setTimeout(() => profileSearchInput.focus(), 50);
            }

            renderSearchResults(searchProfiles);
            openModal(searchProfileModal);
        });
    });

    if (typeof window.openScoreAfterLoad !== "undefined" && window.openScoreAfterLoad) {
        openModal(scoreModal);
    }

    if (typeof window.openProfileEditAfterLoad !== "undefined" && window.openProfileEditAfterLoad) {
        openModal(editProfileModal);
    }

    if (typeof window.openCommentAfterLoad !== "undefined" && window.openCommentAfterLoad) {
        openModal(commentModal);
    }

    if (typeof window.openContactAfterLoad !== "undefined" && window.openContactAfterLoad) {
        openModal(contactModal);
    }

    const heroAvatarButton = document.getElementById("heroAvatarButton");
    const heroAvatarFrame = document.getElementById("heroAvatarFrame");

    function initHeroAvatarFrames() {
        if (!heroAvatarButton || !heroAvatarFrame) return;

        const userId = heroAvatarButton.dataset.userId || "default";
        const storageKey = `neear_profile_frame_${userId}`;

        const frames = [
            "",
            "assets/frames/pinceau.svg",
            "assets/frames/sunglasses.svg",
            "assets/frames/ordinateur.svg",
            "assets/frames/telephone.svg",
            "assets/frames/fleur.svg",
            "assets/frames/halloween.svg",
            "assets/frames/nvlan_chinois.svg",
            "assets/frames/paque.svg",
            "assets/frames/paque2.svg",
            "assets/frames/trefle.svg",
            "assets/frames/soleil.svg"
        ];

        let currentIndex = parseInt(localStorage.getItem(storageKey) || "0", 10);

        if (isNaN(currentIndex) || currentIndex < 0 || currentIndex >= frames.length) {
            currentIndex = 0;
        }

        function applyFrame(index) {
            const src = frames[index];

            if (!src) {
                heroAvatarFrame.style.display = "none";
                heroAvatarFrame.src = "";
            } else {
                heroAvatarFrame.src = src;
                heroAvatarFrame.style.display = "block";
            }

            localStorage.setItem(storageKey, String(index));
        }

        applyFrame(currentIndex);

        heroAvatarButton.addEventListener("click", function () {
            currentIndex++;
            if (currentIndex >= frames.length) {
                currentIndex = 0;
            }
            applyFrame(currentIndex);
        });
    }

    initHeroAvatarFrames();

    initFallingPosts();
});


function initFallingPosts() {
    const wrap = document.getElementById("postsFallWrap");
    if (!wrap) return;

    const allPosts = Array.from(wrap.querySelectorAll(".falling-post"));
    if (!allPosts.length) return;

    const visibleCount = Math.max(1, Math.ceil(allPosts.length * (2 / 3)));

    allPosts.forEach((post, index) => {
        if (index >= visibleCount) {
            post.remove();
        }
    });

    const posts = Array.from(wrap.querySelectorAll(".falling-post"));
    if (!posts.length) return;

    function placePost(post) {
        const wrapHeight = wrap.clientHeight;
        const wrapWidth = wrap.clientWidth;

        const cardWidth = Math.min(340, Math.max(220, wrapWidth - 30));
        const maxLeft = Math.max(10, wrapWidth - cardWidth - 10);

        const left = random(10, maxLeft);
        const startY = random(-800, -140);
        const drift = random(-35, 35);
        const rotateStart = random(-8, 8);
        const rotateMid = rotateStart + random(-4, 4);
        const rotateEnd = rotateStart + random(-7, 7);
        const duration = random(12, 19);
        const delay = random(-12, 0);
        const scale = random(0.97, 1.02);

        post.style.width = `${cardWidth}px`;
        post.style.left = `${left}px`;
        post.style.top = `0px`;

        post.style.setProperty("--startY", `${startY}px`);
        post.style.setProperty("--endY", `${wrapHeight + 260}px`);
        post.style.setProperty("--drift", `${drift}px`);
        post.style.setProperty("--rotateStart", `${rotateStart}deg`);
        post.style.setProperty("--rotateMid", `${rotateMid}deg`);
        post.style.setProperty("--rotateEnd", `${rotateEnd}deg`);
        post.style.setProperty("--scalePost", scale);

        post.style.animation = "none";
        void post.offsetHeight;
        post.style.animation = `fallPost ${duration}s linear ${delay}s infinite`;
    }

    posts.forEach((post) => {
        placePost(post);
    });

    let resizeTimeout = null;
    window.addEventListener("resize", function () {
        clearTimeout(resizeTimeout);

        resizeTimeout = setTimeout(function () {
            posts.forEach((post) => {
                placePost(post);
            });
        }, 150);
    });
}