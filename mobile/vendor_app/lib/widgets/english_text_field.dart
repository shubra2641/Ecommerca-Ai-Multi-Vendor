import 'package:flutter/material.dart';

/// A thin wrapper around [TextField]/[TextFormField] that forces left-to-right
/// text direction and suggests the English (Latin) keyboard where possible.
///
/// Usage:
/// EnglishTextField(
///   controller: _nameController,
///   decoration: InputDecoration(labelText: 'Name'),
/// )
class EnglishTextField extends StatelessWidget {
  final TextEditingController? controller;
  final InputDecoration? decoration;
  final TextInputType? keyboardType;
  final bool obscureText;
  final String? initialValue;
  final ValueChanged<String>? onChanged;

  const EnglishTextField({
    Key? key,
    this.controller,
    this.decoration,
    this.keyboardType,
    this.obscureText = false,
    this.initialValue,
    this.onChanged,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Directionality(
      textDirection: TextDirection.ltr,
      child: TextFormField(
        controller: controller,
        initialValue: initialValue,
        decoration: decoration,
        keyboardType: keyboardType ?? TextInputType.text,
        obscureText: obscureText,
        onChanged: onChanged,
        textDirection: TextDirection.ltr,
      ),
    );
  }
}
