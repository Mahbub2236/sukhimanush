class ChamberModel {
  final int id;
  final String chamberName;
  final String address;
  final String? city;
  final String? consultationDays;
  final String? consultationTime;
  final double consultationFee;
  final bool isPrimary;
  final bool isActive;

  ChamberModel({
    required this.id,
    required this.chamberName,
    required this.address,
    this.city,
    this.consultationDays,
    this.consultationTime,
    required this.consultationFee,
    required this.isPrimary,
    required this.isActive,
  });

  factory ChamberModel.fromJson(Map<String, dynamic> json) {
    String? days;
    if (json['consultation_days'] != null) {
      days = json['consultation_days'] is List
          ? (json['consultation_days'] as List).join(', ')
          : json['consultation_days'].toString();
    } else if (json['visiting_days'] != null) {
      days = json['visiting_days'] is List
          ? (json['visiting_days'] as List).join(', ')
          : json['visiting_days'].toString();
    }

    String? time;
    if (json['consultation_time'] != null) {
      time = json['consultation_time'].toString();
    } else if (json['start_time'] != null) {
      time = '${json['start_time']} - ${json['end_time'] ?? ''}';
    }

    return ChamberModel(
      id: int.tryParse(json['id'].toString()) ?? 0,
      chamberName: json['chamber_name']?.toString() ?? json['name']?.toString() ?? 'Chamber',
      address: json['address']?.toString() ?? '',
      city: json['city']?.toString(),
      consultationDays: days,
      consultationTime: time,
      consultationFee: (json['consultation_fee'] != null)
          ? double.tryParse(json['consultation_fee'].toString()) ?? 0.0
          : 0.0,
      isPrimary: json['is_primary'] == true ||
          json['is_primary'] == 1 ||
          json['is_primary'] == '1',
      isActive: json['is_active'] == true ||
          json['is_active'] == 1 ||
          json['is_active'] == '1',
    );
  }
}

class DoctorModel {
  final int id;
  final String name;
  final String slug;
  final String specialization;
  final String? designation;
  final String? qualifications;
  final String? bmdcNumber;
  final double consultationFee;
  final String? image;
  final double rating;
  final int totalReviews;
  final int? experienceYears;
  final bool isFeatured;
  final bool isApproved;
  final String? division;
  final String? district;
  final String? bio;
  final List<ChamberModel> chambers;

  DoctorModel({
    required this.id,
    required this.name,
    required this.slug,
    required this.specialization,
    this.designation,
    this.qualifications,
    this.bmdcNumber,
    required this.consultationFee,
    this.image,
    required this.rating,
    required this.totalReviews,
    this.experienceYears,
    required this.isFeatured,
    required this.isApproved,
    this.division,
    this.district,
    this.bio,
    required this.chambers,
  });

  factory DoctorModel.fromJson(Map<String, dynamic> json) {
    var rawChambers = json['chambers'] as List? ?? [];
    List<ChamberModel> chamberList = [];
    for (var c in rawChambers) {
      if (c is Map) {
        try {
          chamberList.add(ChamberModel.fromJson(Map<String, dynamic>.from(c)));
        } catch (_) {}
      }
    }

    // Resolve full image URL
    String? rawImage = json['image']?.toString() ??
        json['avatar_url']?.toString() ??
        json['avatar']?.toString();
    String? finalImage;
    if (rawImage != null && rawImage.isNotEmpty) {
      if (rawImage.startsWith('http://') || rawImage.startsWith('https://')) {
        if (rawImage.contains('imagekit.io') ||
            rawImage.contains('127.0.0.1') ||
            rawImage.contains('localhost')) {
          finalImage = rawImage;
        } else {
          // Route external hospital sites without CORS through proxy
          finalImage = 'http://127.0.0.1:8000/api/image-proxy?url=${Uri.encodeComponent(rawImage)}';
        }
      } else {
        // Strip leading slash if any and append to base origin
        final cleanPath = rawImage.startsWith('/') ? rawImage.substring(1) : rawImage;
        // In local dev, prepend the backend host
        finalImage = 'http://127.0.0.1:8000/$cleanPath';
      }
    }

    return DoctorModel(
      id: int.tryParse(json['id'].toString()) ?? 0,
      name: json['name']?.toString() ?? '',
      slug: json['slug']?.toString() ?? '',
      specialization: json['specialization']?.toString() ?? '',
      designation: json['designation']?.toString(),
      qualifications: json['qualifications']?.toString(),
      bmdcNumber: json['bmdc_number']?.toString(),
      consultationFee: (json['consultation_fee'] != null)
          ? double.tryParse(json['consultation_fee'].toString()) ?? 500.0
          : 500.0,
      image: finalImage,
      rating: (json['rating'] != null)
          ? double.tryParse(json['rating'].toString()) ?? 4.9
          : 4.9,
      totalReviews: (json['total_reviews'] != null)
          ? int.tryParse(json['total_reviews'].toString()) ?? 42
          : (json['appointments_count'] != null
              ? int.tryParse(json['appointments_count'].toString()) ?? 42
              : 42),
      experienceYears: (json['experience_years'] != null)
          ? int.tryParse(json['experience_years'].toString())
          : null,
      isFeatured: json['is_featured'] == true ||
          json['is_featured'] == 1 ||
          json['is_featured'] == '1',
      isApproved: json['is_approved'] == true ||
          json['is_approved'] == 1 ||
          json['is_approved'] == '1',
      division: json['division']?.toString(),
      district: json['district']?.toString(),
      bio: json['bio']?.toString(),
      chambers: chamberList,
    );
  }
}

